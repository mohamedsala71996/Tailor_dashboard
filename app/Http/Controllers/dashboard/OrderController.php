<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Imports\MasterOrderImport;
use App\Imports\OrderImport;
use App\Imports\ProductImport;
use App\Models\Batch;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\ExcelSheet;
use App\Models\MasterOrder;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Excel;
use Illuminate\Support\Str;



class OrderController extends Controller
{


    function __construct()
    {
        $this->middleware('permissionMiddleware:اضافة-شيت الاكسل')->only('importOrder');
    }

    public function index($tailor_id)
    {
        session()->forget('show_more');

        // Retrieve the tailor (user)
        $tailor = User::find($tailor_id);

        // Build the query
        $query = ExcelSheet::where('user_id', $tailor_id);

        // Add filters for track_number, created_at, and name if they exist in the request
        if (request()->filled('track_number')) {
            $query->where('track_number', 'LIKE', '%' . request('track_number') . '%');
        }

        if (request()->filled('created_at')) {
            $query->whereDate('created_at', request('created_at'));
        }

        if (request()->filled('name')) {
            $query->where('name', 'LIKE', '%' . request('name') . '%');
        }

        // Execute the query to get the filtered batches
        $excel_sheets = $query->get();

        // Return the view with the tailor and batches
        return view('dashboard.tailors.index')->with([
            'tailor' => $tailor,
            'excel_sheets' => $excel_sheets,
        ]);
    }



    // public function show($batch_id)
    // {
    //     $batch = Batch::where('id', $batch_id)->first();

    //     $tailor = User::find($batch->tailor_id);


    //     if (!$batch) {
    //         return redirect()->back()->with('error', 'الدفعة غير موجودة لهذا الخياط.');
    //     }

    //     // Fetching all orders related to the batch
    //     $orders = Order::with('masterOrder.product') // Assuming relationships
    //         ->where('batch_id', $batch->id)
    //         ->get()
    //         ->groupBy('masterOrder.product_id');

    //     $groupedOrders = [];

    //     // Grouping orders by product and size
    //     foreach ($orders as $productId => $productOrders) {
    //         foreach ($productOrders as $order) {
    //             $sizeId = $order->masterOrder->size_id;

    //             // Initialize the array for the product if it doesn't exist
    //             if (!isset($groupedOrders[$productId])) {
    //                 $groupedOrders[$productId] = [];
    //             }

    //             // Add order details for the specific size
    //             $groupedOrders[$productId][$sizeId] = [
    //                 'master_order_id' => $order->masterOrder->id,
    //                 'quantity_requested' => $order->quantity_requested,
    //                 'order_id' => $order->id,
    //                 'quantity_delivered_tailor' => $order->quantity_delivered_tailor,
    //                 'quantity_delivered_supervisor' => $order->quantity_delivered_supervisor,
    //                 'remaining_quantity_admin' => $order->remaining_quantity_admin,
    //             ];
    //         }
    //     }

    //     return view('dashboard.tailors.show')->with([
    //         'tailor' => $tailor,
    //         'batch' => $batch,
    //         'groupedOrders' => $groupedOrders,
    //     ]);
    // }

    public function importOrder(Request $request, $tailor_id)
    {

        $request->validate([
            'excel' => 'required|mimes:xlsx,doc,docx,ppt,pptx,ods,odt,odp',
            'name' => 'required|string|max:255'
        ]);
        $name = $request->name;

        Excel::import(new MasterOrderImport($tailor_id, $name), request()->file('excel'));

        return redirect()->back()
            ->with('success', 'تم اضافة الطلبية بنجاح');
    }


    public function masterOrder($excel_sheet_id)
    {
        // Retrieve the excel sheet with related master orders, products, and sizes
        $excel_sheet = ExcelSheet::with(['masterOrders.product', 'masterOrders.size'])
            ->where('id', $excel_sheet_id)
            ->first();

        if (!$excel_sheet) {
            return redirect()->back()->with('error', 'لا توجد طلبيات');
        }

        // Initialize the array to store grouped orders by product and size
        $groupedOrders = [];

        // Iterate over each master order related to the excel sheet
        foreach ($excel_sheet->masterOrders as $masterOrder) {
            $productId = $masterOrder->product_id;
            $sizeId = $masterOrder->size_id;

            // Fetch all batches for the product and user
            $batches = Batch::where('user_id', $excel_sheet->user_id)
                ->where('product_id', $productId)
                ->get();

            // Initialize an array to store batch information
            $batchData = [];

            // Loop through each batch
            foreach ($batches as $batch) {
                // Fetch all orders related to the master order and the current batch
                $orders = Order::where('master_order_id', $masterOrder->id)
                    ->where('batch_id', $batch->id)
                    ->get();

                // Store batch and orders details in the batchData array
                $batchData[] = [
                    'batch_id' => $batch->id,
                    'created_at' => $batch->created_at ?? 'No Date Available',
                    'track_number' => $batch->track_number ?? 'No track number Available',
                    'orders' => $orders->map(function ($order) {
                        return [
                            'order_id' => $order->id,
                            'quantity_delivered_tailor' => $order->quantity_delivered_tailor,
                            'quantity_delivered_supervisor' => $order->quantity_delivered_supervisor,
                            'remaining_quantity_admin' => $order->remaining_quantity_admin,
                        ];
                    })->toArray(),
                ];
            }

            // Group orders by product and size
            if (!isset($groupedOrders[$productId])) {
                $groupedOrders[$productId] = [];
            }

            // Add the batch data to the grouped orders under the product and size
            $groupedOrders[$productId][$sizeId] = [
                'master_order_id' => $masterOrder->id,
                'quantity_requested' => $masterOrder->quantity_requested,
                // 'batches' => $batchData, // Store batch information with orders
            ];
        }

        // Return the view with the grouped orders data
        return view('dashboard.tailors.last-order', compact('excel_sheet', 'groupedOrders'));
    }


}
