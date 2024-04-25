<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sold;
use Illuminate\Support\Facades\Auth;

class BuyingHistoryController extends Controller
{
    public function index() {
        return view('users.buyinghistory');
    }

    public function show() {
        $user_id = Auth::id();
        $data = Sold::leftjoin('users', 'users.id', 'solds.user_id')
                ->leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'solds.tools_and_equipment_id')
                ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id') 
                ->select('solds.*', 'users.name as user_name', 'products.brand as brand_name', 'products.tool as tool_name',
                 'tools_and_equipment.product_code as product_code', 'tools_and_equipment.price as price')
                ->where('tools_and_equipment.status', 'Sold')
                ->where('solds.user_id', $user_id)
                ->get();

        return response()->json([ 'data' => $data]);
    }

    public function filterData(Request $request) {
        $brand = $request->brand;
        $tool = $request->tool;
    
        $query = Sold::query();

        $query->leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'solds.tools_and_equipment_id')
                ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                ->select('solds.*', 'products.brand as brand_name', 'products.tool as tool_name',
                'tools_and_equipment.product_code as product_code', 'tools_and_equipment.price as price');

        if (!empty($brand)) {
            $query->where('products.brand', 'like', '%' . $brand . '%');
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }

        $data = $query->get();
        
        return response()->json(['data' => $data]);
    }
}
