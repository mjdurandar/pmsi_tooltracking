<?php

namespace App\Http\Controllers;

use App\Models\CanceledOrder;
use Illuminate\Http\Request;

class CanceledOrderController extends Controller
{
    public function index()
    {
        return view('admin.canceled-order');
    }

    public function show()
    {
        $data = CanceledOrder::leftjoin('track_orders', 'track_orders.id', 'canceled_orders.track_order_id')
                            ->leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'track_orders.tools_and_equipment_id')
                            ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                            ->select('canceled_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image',
                             'track_orders.created_at as placed_order_at', 'canceled_orders.created_at as canceled_order_at', 'products.image as image'
                             ,'products.voltage as voltage', 'products.powerSources as powerSources', 'products.weight as weight', 'products.dimensions as dimensions', 'products.material as material')
                            ->get();      

        return response()->json([
            'data' => $data
        ]);
    }
}
