<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    public function index(){
        return view('admin.unit');
    }

    public function show(){
        $data = Unit::get();
        return response()->json([ 'data' => $data, ]);
    }

    public function store(Request $request)
    {       
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => "The Name field is required",
        ]);

        $data = isset($request->id) ? Unit::where('id', $request->id)->first() : new Unit();
        $data->name = $request->name;
        $data->save();
        return response()->json(['message' => 'Data Successfully Saved']);
    }

    public function edit(Unit $unit)
    {
        return response()->json(['data' => $unit]);
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return response()->json(['data' => $unit]);
    }
}
