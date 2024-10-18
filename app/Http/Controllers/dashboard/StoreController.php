<?php

namespace App\Http\Controllers\dashboard;

use App\Models\Store;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    function __construct()
    {
        // $this->middleware('permissionMiddleware:تبديل-المتاجر')->only('switchStore');
        $this->middleware('permissionMiddleware:قراءة-المتاجر')->only('index');
        $this->middleware('permissionMiddleware:حذف-المتاجر')->only('destroy');
        $this->middleware('permissionMiddleware:تعديل-المتاجر')->only(['edit', 'update']);
        $this->middleware('permissionMiddleware:اضافة-المتاجر')->only(['create', 'store']);
    }

    public function switchStore($storeId)
    {
        $store = Store::find($storeId);

        if ($store) {
            // Get the currently authenticated user
            $user = auth()->user();

            // Check if the user is not null
            if ($user) {
                // Update the user's store_id
                $user->store_id = $storeId;
                $user->save(); // Use save instead of update
            }

            // Optionally, you can redirect to a specific page (like dashboard) with the new store context
            return redirect()->route('dashboard.home')->with('success', 'تم تبديل المتجر بنجاح');
        }

        return redirect()->back()->with('error', 'المتجر غير موجود');
    }




    public function index()
    {
        $stores = Store::all();
        return view('dashboard.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('dashboard.stores.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10000', // Validate the photo field
        ]);

        // Handle the file upload for 'photo'
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('stores_photos', 'public');
        }

        Store::create($data);

        return redirect()->route('dashboard.stores.index')->with('success', 'تم إنشاء المتجر بنجاح.');
    }

    public function edit($id)
    {
        $store = Store::findOrFail($id);
        $stores = Store::all(); // For the store list
        return view('dashboard.stores.index', compact('store', 'stores')); // Pass both store and stores
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10000', // Validate the photo field
        ]);

        $store = Store::findOrFail($id);

        // Handle the file upload for 'photo'
        if ($request->hasFile('photo')) {
            // Delete the old photo if it exists
            if ($store->photo) {
                Storage::disk('public')->delete($store->photo);
            }

            // Store the new photo
            $data['photo'] = $request->file('photo')->store('stores_photos', 'public');
        }

        $store->update($data);

        return redirect()->route('dashboard.stores.index')->with('success', 'تم تحديث المتجر بنجاح.');
    }


    public function destroyStore($id)
    {
        $store = Store::findOrFail($id);

        // Delete the associated photo if it exists
        if ($store->photo) {
            Storage::disk('public')->delete($store->photo);
        }

        // Delete the store
        $store->delete();

        return redirect()->route('dashboard.stores.index')->with('success', 'تم حذف المتجر بنجاح.');
    }
}
