<?php

namespace App\Http\Controllers;

use App\Models\PowerTools;
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

        $categories = Category::get();
        $units = Unit::get();
        $suppliers = Supplier::get();
        $data = PowerTools::leftjoin('categories', 'categories.id', '=', 'power_tools.category_id')
                            ->leftjoin('units', 'units.id', '=', 'power_tools.unit_id')
                            ->leftjoin('suppliers', 'suppliers.id', '=', 'power_tools.supplier_id')
                            ->select('power_tools.*','categories.name as category_name', 'units.name as unit_name', 'suppliers.name as supplier_name')
                            ->get();

        return response()->json([ 'data' => $data, 'categories' => $categories, 'units' => $units, 'suppliers' => $suppliers]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if an ID is provided
        if (isset($request->id)) {
            // Attempt to find the existing record
            try {
                $powerTool = PowerTools::findOrFail($request->id);
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('Error finding PowerTool by ID: ' . $e->getMessage());

                // Return a response indicating that the record was not found
                return response()->json(['error' => 'Power Tool not found'], 404);
            }
        } else {
            // If no ID is provided, create a new instance
            $powerTool = new PowerTools();
        }

        $productCode = 'P-' . str_pad(PowerTools::count() + 1, 3, '0', STR_PAD_LEFT);

        $powerTool = isset($request->id) ? PowerTools::where('id', $request->id)->first() : new PowerTools();
        $powerTool->name = $request->name;
        $powerTool->price = $request->price;
        $powerTool->category_id = $request->category_id;
        $powerTool->unit_id = $request->unit_id;
        $powerTool->supplier_id = $request->supplier_id;
        $powerTool->product_code = $productCode;

        // Check if an image file is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $powerTool->image = $imageName;
        }

        $powerTool->save();

        return response()->json(['message' => 'Power Tool added successfully'], 200);
    }

    public function edit(PowerTools $powertools)
    {
        return response()->json(['data' => $powertools]);
    }

    public function destroy(PowerTools $powertools)
    {
        $powertools->delete();
        return response()->json(['data' => $powertools]);
    }
}
