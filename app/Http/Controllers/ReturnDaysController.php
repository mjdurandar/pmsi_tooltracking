<?php

namespace App\Http\Controllers;

use App\Models\ReturnDays;
use Illuminate\Http\Request;

class ReturnDaysController extends Controller
{
    public function index() {
        return view('admin.returndays');
    }

    public function show() {
        
        $data = ReturnDays::get();
        return response()->json([ 'data' => $data, ]);
    }

    public function store(Request $request) {
        $request->validate([
            'number_of_days' => 'required',
            'penalty' => 'required',
        ], [
            'number_of_days.required' => "The Number of Days field is required",
            'penalty.required' => "The Penalty field is required",
        ]);

        $data = isset($request->id) ? ReturnDays::where('id', $request->id)->first() : new ReturnDays();
        $data->number_of_days = $request->number_of_days;
        $data->penalty = $request->penalty;
        $data->save();
        return response()->json(['message' => 'Data Successfully Saved']);
    }

    public function edit(ReturnDays $returndays)
    {
        return response()->json(['data' => $returndays]);
    }

    public function destroy(ReturnDays $returndays)
    {
        $returndays->delete();
        return response()->json(['data' => $returndays]);
    }


}
