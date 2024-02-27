<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier1;
use App\Models\Supplier;

class Supplier1Controller extends Controller
{
    public function show() {
        $data = Supplier1::leftjoin('request_products', 'suppliers.id', '=', 'supplier1.supplier_id')
                                ->select('supplier1.*', 'suppliers.name as supplier_name')
                                ->get();
        $countRequestProduct = Supplier1::count();
        $suppliers = Supplier::get();
        return response()->json([ 'data' => $data, 'countRequestProduct' => $countRequestProduct, 'suppliers' => $suppliers ]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required:description',
            'price' => 'required:price',
            'stocks' => 'required:stocks',
            'supplier_id' => 'required:supplier_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => "The Name field is required",
            'description.required' => "The Description field is required",
            'price.required' => "The Price field is required",
            'stocks.required' => "The Stocks field is required",
            'supplier_id.required' => "The Supplier field is required",
            'image.required' => "The Image field is required",
        ]);

        for ($i = 0; $i < $request->stocks; $i++) {
            // Create a new RequestProduct instance
            $newRequestProduct = new Supplier1();
            $newRequestProduct->name = $request->name;
            $newRequestProduct->description = $request->description;
            $newRequestProduct->price = $request->price;
            $newRequestProduct->stocks = 1; // Each record has one stock
            $newRequestProduct->supplier_id = $request->supplier_id;
    
            // Generate unique product code for each record
            // $productCodePrefix = 'P-' . str_pad(Supplier1::count() + $i + 1, 3, '0', STR_PAD_LEFT);
            // $newRequestProduct->product_code = $productCodePrefix;
    
            if ($i === 0 && $request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName(); // Generate unique image name
                $image->move(public_path('images'), $imageName);
                $newRequestProduct->image = $imageName;
            }    
    
            // Save the new RequestProduct instance
            $newRequestProduct->save();
        }

        return response()->json(['message' => 'Supplier Product added successfully'], 200);
    }

    public function updateStock(){
        
    }

    public function edit(Supplier1 $requestproduct)
    {
        return response()->json(['data' => $requestproduct]);
    }

    public function destroy(Supplier1 $requestproduct)
    {
        $requestproduct->delete();
        return response()->json(['data' => $requestproduct]);
    }
    
}
