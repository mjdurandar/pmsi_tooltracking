<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier1;
use App\Models\Supplier;
use App\Models\ToolsAndEquipment;

class Supplier1Controller extends Controller
{
    public function show() {
        $data = Supplier1::leftjoin('suppliers', 'suppliers.id', '=', 'supplier1s.supplier_id')
                        ->select('supplier1s.*', 'suppliers.name as supplier_name')
                        ->get();
                        
        $suppliers = Supplier::get();
        return response()->json([ 'data' => $data, 'suppliers' => $suppliers ]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required:description',
            'price' => 'required:price',
            'stocks' => 'required:stocks',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => "The Name field is required",
            'description.required' => "The Description field is required",
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
        $newSupplier1 = new Supplier1();
        $newSupplier1->name = $request->name;
        $newSupplier1->description = $request->description;
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
        $requestedQuantity = $request->requestedQuantity;
        $requestedId = $request->requestedId;
        $product = Supplier1::findOrFail($requestedId);
        // Duplicate the product data based on the requested quantity
        for ($i = 0; $i < $requestedQuantity; $i++) {
            $productCode = 'P-' . str_pad(ToolsAndEquipment::count() + 1, 3, '0', STR_PAD_LEFT);
            // Create a new instance of ToolsAndEquipment
            $tool = new ToolsAndEquipment();
            // Assign the product details to the ToolsAndEquipment instance
            $tool->name = $product->name;
            $tool->description = $product->description;
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

    public function edit(Supplier1 $supplier1)
    {
        return response()->json(['data' => $supplier1]);
    }

    public function destroy(Supplier1 $supplier1)
    {
        $supplier1->delete();
        return response()->json(['data' => $supplier1]);
    }
    
}
