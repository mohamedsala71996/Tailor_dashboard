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



class BatchController extends Controller
{


    public function createBatch(Request $request)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Generate a unique track number
            $track_number = strtoupper(Str::random(3)) . rand(100000000, 999999999) . strtoupper(Str::random(2));

            // Check if the last batch exists for the product and excel sheet
            $last_batch = Batch::where('excel_sheet_id', $request->excel_sheet_id)
                ->where('product_id', $request->product_id)
                ->latest()
                ->first();

            if ($last_batch) {
                // Create a new batch
                $batch = Batch::create([
                    'track_number' => $track_number,
                    'user_id' => $request->tailor_id,
                    'product_id' => $request->product_id,
                    'excel_sheet_id' => $request->excel_sheet_id,
                ]);
            //     DB::rollBack();

            //  return   $last_batch->orders;
                // Check and update orders related to the last batch
                foreach ($last_batch->orders as $order) {
                    if ($order->remaining_quantity_admin === null) {
                        // Rollback the transaction and return an error if the current batch is incomplete
                        DB::rollBack();
                        return redirect()->back()->with('error', 'يرجى اكمال الدفعة الحالية اولا');
                    }

                    // Create new orders for the batch
                    Order::create([
                        'master_order_id' => $order->master_order_id,
                        'size_id' => $order->size_id,
                        'batch_id' => $batch->id,
                        'quantity_requested' => $order->remaining_quantity_admin,
                        'quantity_delivered_tailor' => null,
                        'quantity_delivered_supervisor' => null,
                        'remaining_quantity_admin' => null,
                    ]);

                    // Store product ID in the session for further use
                    if ($order->masterOrder->product->id) {
                        session(['show_more' => $order->masterOrder->product->id]);
                    }
                }

            } else {
                // Create a new batch if no last batch exists
                $batch = Batch::create([
                    'track_number' => $track_number,
                    'user_id' => $request->tailor_id,
                    'product_id' => $request->product_id,
                    'excel_sheet_id' => $request->excel_sheet_id,
                ]);

                // Create new orders for the batch from the master orders
                $master_orders = MasterOrder::where('excel_sheet_id', $request->excel_sheet_id)
                    ->where('product_id', $request->product_id)
                    ->get();

                foreach ($master_orders as $master_order) {
                    Order::create([
                        'master_order_id' => $master_order->id,
                        'size_id' => $master_order->size_id,
                        'batch_id' => $batch->id,
                        'quantity_requested' => $master_order->quantity_requested,
                        'quantity_delivered_tailor' => null,
                        'quantity_delivered_supervisor' => null,
                        'remaining_quantity_admin' => null,
                    ]);

                    // Store product ID in the session for further use
                    if ($master_order->product->id) {
                        session(['show_more' => $master_order->product->id]);
                    }
                }
            }

            // Commit the transaction after all operations are successful
            DB::commit();

            // Redirect back with success message
            return redirect()->back()->with('success', 'تم إنشاء جدول صادر بنجاح');

        } catch (\Exception $e) {
            // Rollback the transaction in case of any errors
            DB::rollBack();
            // Optionally log the error for debugging
            Log::error('Error creating batch: ' . $e->getMessage());
            // Redirect back with an error message
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الدفعة');
        }
    }

    public function orderQuantities(Request $request)
    {
        // Initialize a variable to keep track of the product ID
        $productId = null;

        // Update quantities delivered by the tailor
        $quantitiesTailor = $request->input('quantity_delivered_tailor', []);
        foreach ($quantitiesTailor as $OrderId => $quantityDeliveredTailor) {
            $order = Order::find($OrderId);
            if ($order && $quantityDeliveredTailor !== null) {
                $order->update([
                    'quantity_delivered_tailor' => $quantityDeliveredTailor
                ]);
                $productId = $order->masterOrder->product_id; // Store the product ID
            }
        }

        // Update quantities delivered by the supervisor
        $quantitiesSupervisor = $request->input('quantity_delivered_supervisor', []);
        foreach ($quantitiesSupervisor as $OrderId => $quantityDeliveredSupervisor) {
            $order = Order::find($OrderId);
            if ($order && $quantityDeliveredSupervisor !== null) {
                $remainingQuantityAdmin = $order->quantity_requested - $quantityDeliveredSupervisor;
                $order->update([
                    'quantity_delivered_supervisor' => $quantityDeliveredSupervisor,
                    'remaining_quantity_admin' => $remainingQuantityAdmin
                ]);
                $productId = $order->masterOrder->product_id; // Store the product ID
            }
        }

        // Update remaining quantities managed by the admin
        $remainingQuantitiesAdmin = $request->input('remaining_quantity_admin', []);
        foreach ($remainingQuantitiesAdmin as $OrderId => $remainingQuantityAdmin) {
            $order = Order::find($OrderId);
            if ($order && $remainingQuantityAdmin !== null) {
                $order->update([
                    'remaining_quantity_admin' => $remainingQuantityAdmin
                ]);
                $productId = $order->masterOrder->product_id; // Store the product ID
            }
        }

        // Set session variable only if productId is set
        if ($productId) {
            session(['show_more' => $productId]);
        }

        return redirect()->back()->with('success', 'تم تحديث الكميات بنجاح.');
    }




    // public function endBatch(Batch $batch)
    // {
    //     // Update the status or mark the order as complete
    //     $batch->completed = 1;  // Assuming you have a status field
    //     $batch->save();

    //     // Redirect with a success message
    //     return redirect()->route('dashboard.orders.index', $batch->user_id)->with('success', 'Master Order has been completed.');
    // }

    // public function completedMasterOrders($tailor_id)
    // {
    //     // Fetch all master orders where 'completed' is set to 1
    //     $completedMasters = MasterOrder::where('completed', 1)->where('user_id', $tailor_id)
    //         ->with(['orders' => function ($query) {
    //             $query->with('product', 'size'); // Load related products and sizes
    //         }])
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     // Return the view with completed master orders
    //     return view('dashboard.tailors.completed-orders', compact('completedMasters'));
    // }

    public function updateRequestedQuantities(Request $request)
    {

        $productId = $request->input('product_id');
        $requestedQuantities = $request->input('quantity_requested');

        foreach ($requestedQuantities as $size => $quantity) {
            // Ensure the quantity is not null
            if (is_null($quantity)) {
                continue; // Skip if the quantity is null
            }

            // Get the master order
            $master = MasterOrder::where('product_id', $productId)
                ->where('size_id', $size)
                ->first();

            if ($master) {
                // Update the master order with the requested quantity
                $master->update(['quantity_requested' => $quantity]);

                // Get the last batch
                $last_batch = Batch::where('excel_sheet_id', $master->excel_sheet_id)
                    ->where('product_id', $master->product_id)
                    ->first();

                if ($last_batch) {
                    // Update orders in the last batch
                    foreach ($last_batch->orders()->where('size_id', $size)->get() as $order) {
                        $order->quantity_requested = $quantity;
                        if ($order->quantity_delivered_supervisor) {
                            $order->remaining_quantity_admin = $quantity - $order->quantity_delivered_supervisor;
                        }
                        $order->save();
                    }

                    // Update remaining orders from previous batches
                    $previous_batches = Batch::where('id', '!=', $last_batch->id)
                        ->where('excel_sheet_id', $master->excel_sheet_id)
                        ->where('product_id', $master->product_id)
                        ->get();

                    foreach ($previous_batches as $batch) {
                        foreach ($batch->orders()->where('size_id', $size)->get() as $order) {
                            // Get the last batch for this order
                            $last_batch_for_order = Batch::where('id', '<', $batch->id)
                                ->orderBy('id', 'desc')
                                ->first();

                            if ($last_batch_for_order) {
                                $order->quantity_requested = $last_batch_for_order->orders->where('size_id',$size)->first()->remaining_quantity_admin ;
                                if ($order->quantity_delivered_supervisor) {
                                    $order->remaining_quantity_admin = $last_batch_for_order->orders->where('size_id',$size)->first()->remaining_quantity_admin - $order->quantity_delivered_supervisor;
                                }
                                $order->save();
                            }
                        }
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'تم تحديث الكميات المطلوبة بنجاح.');
    }

}
