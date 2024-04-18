<?php

namespace App\Http\Controllers;

use App\Models\DeliverHistory;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index() {
        return view('admin.order');
    }

    public function show () {

        $data = Order::leftjoin('users', 'users.id', '=', 'orders.user_id')
                    ->leftjoin('tools_and_equipment', 'tools_and_equipment.id', '=', 'orders.tools_and_equipment_id')
                    ->leftjoin('products', 'products.id', '=', 'tools_and_equipment.product_id')
                    ->select('orders.*', 'users.name as user_name', 'products.brand as brand_name', 'products.tool as tool_name', 'users.location as location')
                    ->get();

        return response()->json([ 'data' => $data ]);
    }

    public function store(Request $request) {

        $request->validate([
            'status' => 'required',
        ], [
            'status.required' => 'The Status field is required'
        ]);
        
        $order = Order::findOrFail($request->id);
        $order->status = $request->status_data;
        
        $order->save();

        $deliverhistory = DeliverHistory::findOrFail($request->id);
        $deliverhistory->status = $request->status_data;

        $deliverhistory->save();

        return response()->json(['message' => 'Data Successfully Saved']);

    }
}
