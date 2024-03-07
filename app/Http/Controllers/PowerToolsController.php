<?php

namespace App\Http\Controllers;

use App\Models\ToolsAndEquipment;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use Validator;

class PowerToolsController extends Controller
{
    public function index(){
        return view('admin.powertools');
    }
    
    public function show(){
        $data = ToolsAndEquipment::get();

        return response()->json([ 'data' => $data]);
    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string',
    //         'price' => 'required|numeric',
    //         'category_id' => 'required|exists:categories,id',
    //         'unit_id' => 'required|exists:units,id',
    //         'supplier_id' => 'required|exists:suppliers,id',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     $request->validate([
    //         'name' => 'required|string',
    //         'price' => 'required|numeric',
    //         'category_id' => 'required|exists:categories,id',
    //         'unit_id' => 'required|exists:units,id',
    //         'supplier_id' => 'required|exists:suppliers,id',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ], [
    //         'name.required' => "The Name field is required",
    //         'price.required' => "The Price field is required",
    //         'category_id.required' => "The Category field is required",
    //         'unit_id.required' => "The Unit field is required",
    //         'supplier_id.required' => "The Supplier field is required",
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     // Check if an ID is provided
    //     if (isset($request->id)) {
    //         // Attempt to find the existing record
    //         try {
    //             $powerTool = PowerTools::findOrFail($request->id);
    //         } catch (\Exception $e) {
    //             // Log the error for debugging
    //             \Log::error('Error finding PowerTool by ID: ' . $e->getMessage());

    //             // Return a response indicating that the record was not found
    //             return response()->json(['error' => 'Power Tool not found'], 404);
    //         }
    //     } else {
    //         // If no ID is provided, create a new instance
    //         $powerTool = new PowerTools();
    //     }

    //     $productCode = 'P-' . str_pad(PowerTools::count() + 1, 3, '0', STR_PAD_LEFT);

    //     $powerTool = isset($request->id) ? PowerTools::where('id', $request->id)->first() : new PowerTools();
    //     $powerTool->name = $request->name;
    //     $powerTool->price = $request->price;
    //     $powerTool->category_id = $request->category_id;
    //     $powerTool->unit_id = $request->unit_id;
    //     $powerTool->supplier_id = $request->supplier_id;
    //     $powerTool->product_code = $productCode;

    //     // Check if an image file is uploaded
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $imageName = time() . '.' . $image->getClientOriginalExtension();
    //         $image->move(public_path('images'), $imageName);
    //         $powerTool->image = $imageName;
    //     }

    //     $powerTool->save();

    //     return response()->json(['message' => 'Power Tool added successfully'], 200);
    // }

    // public function edit(PowerTools $powertools)
    // {
    //     return response()->json(['data' => $powertools]);
    // }

    // public function destroy(PowerTools $powertools)
    // {
    //     $powertools->delete();
    //     return response()->json(['data' => $powertools]);
    // }
}
