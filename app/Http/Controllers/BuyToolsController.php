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
        
        $user_id = auth()->user()->id;
        // Update is_sold_out field for the purchased tool
        $tool = PowerTools::findOrFail($request->power_tools_id);
        $tool->is_sold_out = true; // Assuming is_sold_out is a boolean field
        $tool->sales_date = now();
        $tool->save();
        // Proceed with storing BuyTools data
        $data = new BuyTools();
        $data->user_id = $user_id;
        $data->power_tools_id = $request->power_tools_id;
        $data->purchased_at = now();
        $data->save();

        $delivery = new Delivery();
        $delivery->user_id = $user_id;
        $delivery->power_tools_id = $request->power_tools_id;
        $delivery->purchased_at = now();
        $delivery->save();
    
        return response()->json(['message' => 'Data Successfully Saved']);
    }
    
}
