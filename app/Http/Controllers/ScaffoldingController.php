<?php

namespace App\Http\Controllers;

use App\Models\ProjectSite;
use Illuminate\Http\Request;
use App\Models\Scaffolding;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use Validator;

class ScaffoldingController extends Controller
{
    public function index(){
        return view('admin.scaffolding');
    }

    public function show(){
        $projects = ProjectSite::get();
        $categories = Category::get();
        $units = Unit::get();
        $suppliers = Supplier::get();
        $data = Scaffolding::leftjoin('project_sites', 'project_sites.id', '=', 'scaffoldings.project_site_id')
                            ->leftjoin('categories', 'categories.id', '=', 'scaffoldings.category_id')
                            ->leftjoin('units', 'units.id', '=', 'scaffoldings.unit_id')
                            ->leftjoin('suppliers', 'suppliers.id', '=', 'scaffoldings.supplier_id')
                            ->select('scaffoldings.*', 'project_sites.name as project_site_name', 'categories.name as category_name', 'units.name as unit_name', 'suppliers.name as supplier_name')
                            ->get();

        return response()->json([ 'data' => $data, 'projects' => $projects, 'categories' => $categories, 'units' => $units, 'suppliers' => $suppliers]);
    }

    public function store(Request $request)
    {      
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'quantity' => 'required|numeric',
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
                $powerTool = Scaffolding::findOrFail($request->id);
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('Error finding Scaffolding by ID: ' . $e->getMessage());

                // Return a response indicating that the record was not found
                return response()->json(['error' => 'Scaffolding not found'], 404);
            }
        } else {
            // If no ID is provided, create a new instance
            $scaffolding = new Scaffolding();
        }

        $scaffolding = isset($request->id) ? Scaffolding::where('id', $request->id)->first() : new Scaffolding();
        $scaffolding->name = $request->name;
        $scaffolding->quantity = $request->quantity;
        $scaffolding->price = $request->price;
        $scaffolding->category_id = $request->category_id;
        $scaffolding->unit_id = $request->unit_id;
        $scaffolding->project_site_id = $request->project_site_id;
        $scaffolding->supplier_id = $request->supplier_id;
        $scaffolding->total = $request->total;

        // Check if an image file is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $scaffolding->image = $imageName;
        }

        $scaffolding->save();

        return response()->json(['message' => 'Data Successfully Saved']);
    }

    public function edit(Scaffolding $scaffolding)
    {
        return response()->json(['data' => $scaffolding]);
    }

    public function destroy(Scaffolding $scaffolding)
    {
        $scaffolding->delete();
        return response()->json(['data' => $scaffolding]);
    }

}
