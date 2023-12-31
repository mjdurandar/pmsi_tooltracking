<?php

namespace App\Http\Controllers;

use App\Models\BuyTools;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\PowerTools;
use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;

class BuyToolsController extends Controller
{
    public function index()
    {
        return view('users.buytools');
    }

    public function history() {
        return view('users.buyinghistory');
    }

    public function showHistory() {
        
        $user_id = Auth::id();
        $data = BuyTools::where('user_id', $user_id)
            ->leftJoin('power_tools', 'power_tools.id', '=', 'buy_tools.power_tools_id')
            ->leftjoin('users', 'users.id', '=', 'buy_tools.user_id')
            ->select('buy_tools.*', 'power_tools.name as power_tools_name', 'users.name as users_name')
            ->get();

        return response()->json([ 'data' => $data]);
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

    public function store(Request $request){

        $request->validate([
            'quantity' => 'required|integer',
            'total' => 'required|integer',
        ],
        [
            'quantity.required' => "The Quantity field is required",
            'total.required' => "The Total field is required",
        ]);
        
        $user_id = auth()->user()->id;
    
        // Retrieve the selected PowerTools and its quantity
        $selectedPowerTools = PowerTools::findOrFail($request->power_tools_id);
        $selectedPowerToolsQuantity = $selectedPowerTools->quantity;
    
        // Ensure there are enough PowerTools in stock
        if ($selectedPowerToolsQuantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient quantity in stock'], 400);
        }
    
        // Proceed with storing BuyTools data
        $data = new BuyTools();
        $data->user_id = $user_id;
        $data->power_tools_id = $request->power_tools_id;
        $data->quantity = $request->quantity;
        $data->total = $request->total;
        $data->purchased_at = now();
        $data->save();

        $delivery = new Delivery();
        $delivery->user_id = $user_id;
        $delivery->power_tools_id = $request->power_tools_id;
        $delivery->quantity = $request->quantity;
        $delivery->total = $request->total;
        $delivery->purchased_at = now();
        $delivery->save();
    
        // Update the quantity of the selected PowerTools
        $selectedPowerTools->quantity -= $request->quantity;
        $selectedPowerTools->save();
    
        return response()->json(['message' => 'Data Successfully Saved']);
    }
    

    public function edit(BuyTools $buytools)
    {
        return response()->json(['data' => $buytools]);
    }
}
