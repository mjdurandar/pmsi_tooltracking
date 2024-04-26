<?php

namespace App\Http\Controllers;

use App\Models\Borrowed;
use App\Models\DeliverHistory;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\PurchasedItems;

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
            'status_data' => 'required',
            'shipment_date' => 'required',
            'delivery_date' => 'required',
        ], [
            'status_data.required' => 'The Status field is required',
            'shipment_date.required' => 'The Shipment Date field is required',
            'delivery_date.required' => 'The Delivery Date field is required'
        ]);
        
        $order = Order::findOrFail($request->id);
        $order->status = $request->status_data;
        $order->shipment_date = $request->shipment_date;
        $order->delivery_date = $request->delivery_date;
        $order->save();
        
        $deliverhistory = DeliverHistory::where('tools_and_equipment_id', $order->tools_and_equipment_id)->first();
        $deliverhistory->status = $request->status_data;
        $deliverhistory->save();

        $purchasedItem = PurchasedItems::where('tools_and_equipment_id', $order->tools_and_equipment_id)->first();
        $purchasedItem->details = $request->status_data;
        $purchasedItem->save();

        $borrowed = Borrowed::where('tools_and_equipment_id', $order->tools_and_equipment_id)->first();
        if($borrowed){
            $borrowed->status = $request->status_data;
            $borrowed->save();
        }

        return response()->json(['message' => 'Data Successfully Saved']);

    }
}
