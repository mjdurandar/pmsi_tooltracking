<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index(){
        return view('admin.supplier');
    }

    public function show(){
        $data = Supplier::get();
        return response()->json([ 'data' => $data, ]);
    }

    public function store(Request $request)
    {       
        
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => "The Name field is required",
        ]);

        $data = isset($request->id) ? Supplier::where('id', $request->id)->first() : new Supplier();
        $data->name = $request->name;
        $data->save();
        return response()->json(['message' => 'Data Successfully Saved']);
    }

    public function edit(Supplier $supplier)
    {
        return response()->json(['data' => $supplier]);
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->json(['data' => $supplier]);
    }

}
