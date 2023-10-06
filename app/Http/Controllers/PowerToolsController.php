<?php

namespace App\Http\Controllers;

use App\Models\PowerTools;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;

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
        
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'unit_id' => 'required',
            'supplier_id' => 'required',
            'total' => 'required',
            'quantity' => 'required',
            'total' => 'required',

        ], [
            'name.required' => "The Name field is required",
            'category_id.required' => "The Category field is required",
            'unit_id.required' => "The Unit field is required",
            'supplier_id.required' => "The Supplier field is required",
            'total.required' => "The Total field is required",
            'quantity.required' => "The Quantity field is required",
            'total.required' => "The Total field is required",
        ]);

        $data = isset($request->id) ? PowerTools::where('id', $request->id)->first() : new PowerTools();
        $data->name = $request->name;
        $data->category_id = $request->category_id;
        $data->unit_id = $request->unit_id;
        $data->supplier_id = $request->supplier_id;
        $data->quantity = $request->quantity;
        $data->price = $request->price;
        $data->total = $request->total;
        $data->save();
        return response()->json(['message' => 'Data Successfully Saved']);
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
