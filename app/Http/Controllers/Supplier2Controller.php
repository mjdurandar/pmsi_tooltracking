<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier2;
use App\Models\Supplier;
use App\Models\ToolsAndEquipment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectSite;

class Supplier2Controller extends Controller
{
    public function show() {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $balance = $user->balance; 

        $data = Supplier2::leftjoin('suppliers', 'suppliers.id', '=', 'supplier2s.supplier_id')
                        ->select('supplier2s.*', 'suppliers.name as supplier_name')
                        ->get();

        $products = Supplier2::get();

        $projects = ProjectSite::get();
                        
        $suppliers = Supplier::get();
        return response()->json([ 'data' => $data, 'suppliers' => $suppliers , 'balance' => $balance, 'products' => $products, 'projects' => $projects]);
    }

    public function filterData(Request $request){

        $brand = $request->input('brand');
        $tool = $request->input('tool');
        $specs = $request->input('specs');

        $data = Supplier2::where('name', 'like', '%' . $brand . '%')
                        ->where('name', 'like', '%' . $tool . '%')
                        ->where('specifications', 'like', '%' . $specs . '%')
                        ->get();

        return response()->json(['data' => $data]);
        
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'specifications' => 'required:specifications',
            'price' => 'required:price',
            'stocks' => 'required:stocks',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => "The Name field is required",
            'specifications.required' => "The Specifications field is required",
            'price.required' => "The Price field is required",
            'stocks.required' => "The Stocks field is required",
        ]);
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName(); 
            $image->move(public_path('images'), $imageName);
        } 

        // $productCode = 'P-' . str_pad(Supplier1::count() + 1, 3, '0', STR_PAD_LEFT);
        // Create a new Supplier1 instance
        $newSupplier1 = new Supplier2();
        $newSupplier1->name = $request->name;
        $newSupplier1->specifications = $request->specifications;
        // $newSupplier1->product_code = $productCode;
        $newSupplier1->price = $request->price;
        $newSupplier1->stocks =  $request->stocks; // Each record has one stock
        $newSupplier1->supplier_id = 1;

        // Associate the image with the record
        $newSupplier1->image = $imageName;

        // Save the new Supplier1 instance
        $newSupplier1->save();
    
    
        return response()->json(['message' => 'Supplier Products added successfully'], 200);
    }
    
    public function requestProduct(Request $request){
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);

        $requestedQuantity = $request->requestedQuantity;
        $requestedId = $request->requestedId;
        $total = $request->total; 

        // Deduct the total from the user's balance
        $user->balance -= $total;
        $user->save();

        $product = Supplier2::findOrFail($requestedId);
        // Duplicate the product data based on the requested quantity
        for ($i = 0; $i < $requestedQuantity; $i++) {
            $productCode = 'P-' . str_pad(ToolsAndEquipment::count() + 1, 3, '0', STR_PAD_LEFT);
            // Create a new instance of ToolsAndEquipment
            $tool = new ToolsAndEquipment();
            // Assign the product details to the ToolsAndEquipment instance
            $tool->name = $product->name;
            $tool->specifications = $product->specifications;
            $tool->image = $product->image;
            $tool->price = $product->price;
            $tool->product_code = $productCode;
            $tool->stocks =  1; 
            $tool->supplier_id = $product->supplier_id;
            // Save the duplicated product to the ToolsAndEquipment table
            $tool->save();
        }

        // Update the stocks in the Supplier1 table
        $product->decrement('stocks', $requestedQuantity);

        return response()->json(['message' => 'Products Requested Successfully'], 200);
    }

    public function edit(Supplier2 $supplier2)
    {
        return response()->json(['data' => $supplier2]);
    }

    public function destroy(Supplier2 $supplier2)
    {
        $supplier2->delete();
        return response()->json(['data' => $supplier2]);
    }
    
}
