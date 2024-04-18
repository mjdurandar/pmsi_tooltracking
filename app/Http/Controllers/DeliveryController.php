<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliverHistory;
use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index() {
        return view('users.delivery');
    }

    public function show() {
        $id = Auth::id();

        $data = DeliverHistory::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'deliver_histories.tools_and_equipment_id')
                                ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                                ->select('deliver_histories.*', 'products.brand as brand_name', 'products.tool as tool_name',
                                'tools_and_equipment.product_code as product_code', 'tools_and_equipment.price as price')
                                ->where('deliver_histories.user_id', $id)
                                ->get();

        return response()->json([ 'data' => $data ]);
    }

    public function filterData(Request $request) {
        $brand = $request->brand;
        $tool = $request->tool;
    
        $query = DeliverHistory::query();

        $query->leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'deliver_histories.tools_and_equipment_id')
                ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                ->select('deliver_histories.*', 'products.brand as brand_name', 'products.tool as tool_name',
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
