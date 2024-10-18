<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Size;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductImport;
use App\Http\Controllers\Controller;
use Excel;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    function __construct()
    {
        $this->middleware('permissionMiddleware:قراءة-المنتجات')->only('index');
        $this->middleware('permissionMiddleware:حذف-المنتجات')->only('destroy');
        $this->middleware('permissionMiddleware:تعديل-المنتجات')->only(['edit', 'update']);
        $this->middleware('permissionMiddleware:اضافة-المنتجات')->only(['create', 'store']);
    }
    public function index(Request $request)
    {
        // Fetch the sizes
        $sizes = Size::distinct()->orderBy('name', 'asc')->get();

        // Fetch the search term from the request
        $searchName = $request->get('name');

        // Filter products based on the search term
        $products = Product::when($searchName, function ($query) use ($searchName) {
            return $query->where('name', 'LIKE', '%' . $searchName . '%');
        })->get();

        return view('dashboard.products.index', compact('sizes', 'products'));
    }

    // public function index(Request $request)
    // {


    //     $sizes = Size::distinct()->orderBy('name', 'asc')->get();

    //     $products = Product::get();



    //     // if ($request->ajax()) {

    //     //     $products = Product::with(['sizes']);

    //     //     if (request('size')) {
    //     //         $products->whereRelation('sizes', 'name', request('size'));

    //     //     }




    //     //     if (request('name')) {
    //     //         $products->where('name', "LIKE" , '%' . request('name') . "%");
    //     //     }


    //     //     return Datatables($products)

    //     //         ->addColumn('size', function ($product) {

    //     //             $sizeProduct = '';

    //     //             foreach ($product->sizes as $size) {

    //     //                 $sizeProduct .= $size->name;
    //     //             }
    //     //             return $sizeProduct;
    //     //         })
    //     //         // ->with('count', function () use ($products) {
    //     //         //     return $products->count();
    //     //         // })

    //     //         ->addColumn('actions', 'dashboard.products.actions')

    //     //         ->addColumn('checkboxDelete', 'dashboard.products.checkboxDelete')
    //     //         ->setRowId('row-{{$id}}')

    //     //         ->editColumn('created_at', function ($product) {

    //     //             return $product->created_at->format('Y-m-d H:i:s');
    //     //         })
    //     //         ->rawColumns(['actions','checkboxDelete'])
    //     //         ->addIndexColumn()
    //     //         ->make(true);
    //     // }

    //     return view('dashboard.products.index', compact('sizes','products'));
    // }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // $sizes = Size::distinct()->orderBy('name', 'asc')->get();
        $products=Product::orderBy('name', 'asc')->get();


        return view('dashboard.products.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:10000',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $originalPhotoName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);

                $photoNameWithExtension = $photo->getClientOriginalName();


                $photo->move(public_path('uploads/photos'), $photoNameWithExtension);
                $product = Product::where('name', $originalPhotoName)->first();

                if ($product) {
                    // Update the product's photo if it exists
                    $product->update([
                        'photo' => $photoNameWithExtension,
                    ]);
                } else {
                    // Create a new product if it does not exist
                    Product::create([
                        'name' => $originalPhotoName,
                        'photo' => $photoNameWithExtension,
                    ]);
                }

            }
        }

        return redirect()->route('dashboard.products.index')->with('success', 'تم اضافة المنتجات بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sizes = Size::distinct()->orderBy('name', 'asc')->get();

        $product = Product::findOrFail($id);

        return view('dashboard.products.edit', compact('product', 'sizes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10000', // Validate the uploaded photo if it's present
        ]);

        // Find the product by id
        $product = Product::findOrFail($id);

        // Check if a new photo is uploaded
        if ($request->hasFile('photo')) {
            // Delete the old photo if it exists
            if ($product->photo) {
                $oldPhotoPath = public_path('uploads/photos/' . $product->photo);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath); // Remove the old photo
                }
            }

            // Get the uploaded photo
            $photo = $request->file('photo');
            $originalPhotoName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME); // Get the original file name
            $photoNameWithExtension = $originalPhotoName . '.' . $photo->getClientOriginalExtension(); // Get the full file name with extension

            // Move the new photo to the upload directory
            $photo->move(public_path('uploads/photos'), $photoNameWithExtension);

            // Save the new photo name in the product's photo field
            $product->name = $originalPhotoName;
            $product->photo = $photoNameWithExtension;
            $product->save();

        }
        return redirect()->back()
            ->with('success', 'تم تعديل المنتج بنجاح');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->photo && file_exists(public_path('uploads/photos/' . $product->photo))) {
            unlink(public_path('uploads/photos/' . $product->photo)); // Delete the photo from the server
        }
        $product->delete();
        return redirect()->back()->with('success', 'تم حذف المنتج بنجاح');
    }
    // public function destroyById(Request $request)
    // {

    //     $ids = $request->ids;

    //     $products = Product::whereIn('id',$ids);

    //     $products->delete();

    //     return response()->json([
    //         'status' => 204,
    //         'data' =>  $products,
    //     ]);

    // }

    // public function destroyall()
    // {

    //     Product::query()->delete();

    //     return redirect()->route('dashboard.products.index')->with('success', 'تم حذف المنتجات بنجاح');
    // }

    // public function importProductPage()
    // {
    //     return view('dashboard.products.import-product');
    // }
    // public function importProduct(Request $request)
    // {

    //     $request->validate([
    //         'excel' => 'required|mimes:xlsx,doc,docx,ppt,pptx,ods,odt,odp',
    //     ]);

    //     Excel::import(new ProductImport, request()->file('excel'));

    //     return redirect()->route('dashboard.products.index')
    //         ->with('success', 'تم اضافة المنتج بنجاح');
    // }
}
