<?php

namespace App\Http\Controllers;

use App\Models\ProjectSite;
use Illuminate\Http\Request;
use App\Models\Scaffolding;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;

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
        
        $request->validate([
            'name' => 'required',
            'project_site_id' => 'required',
            'category_id' => 'required',
            'unit_id' => 'required',
            'supplier_id' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'total' => 'required',
        ], [
            'name.required' => "The Name field is required",
            'project_site_id.required' => "The Project Site field is required",
            'category_id.required' => "The Category field is required",
            'unit_id.required' => "The Unit field is required",
            'supplier_id.required' => "The Supplier field is required",
            'quantity.required' => "The Quantity field is required",
            'price.required' => "The Price field is required",
            'total.required' => "The Total field is required",
        ]);

        $data = isset($request->id) ? Scaffolding::where('id', $request->id)->first() : new Scaffolding();
        $data->name = $request->name;
        $data->project_site_id = $request->project_site_id;
        $data->category_id = $request->category_id;
        $data->unit_id = $request->unit_id;
        $data->supplier_id = $request->supplier_id;
        $data->quantity = $request->quantity;
        $data->price = $request->price;
        $data->total = $request->total;
        $data->save();
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
