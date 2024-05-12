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
                            ->leftjoin('products', 'products.id', 'track_orders.product_id')
                            ->select('canceled_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image',
                             'track_orders.created_at as placed_order_at', 'canceled_orders.created_at as canceled_order_at', 'products.image as image'
                             ,'products.voltage as voltage', 'products.powerSources as powerSources', 'products.weight as weight', 'products.dimensions as dimensions', 
                             'products.material as material', 'track_orders.total_price as total_price')
                            ->get();      

        // Format date strings
        $data->transform(function ($item) {
            $item->placed_order_at = $item->placed_order_at ? \Carbon\Carbon::parse($item->placed_order_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            $item->canceled_order_at = $item->canceled_order_at ? \Carbon\Carbon::parse($item->canceled_order_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });
        
        return response()->json([
            'data' => $data
        ]);
    }

    public function filterData(Request $request)
    {
        $brand = $request->brand;
        $tool = $request->tool;
    
        $query = CanceledOrder::leftjoin('track_orders', 'track_orders.id', 'canceled_orders.track_order_id')
        ->leftjoin('products', 'products.id', 'track_orders.product_id')
        ->select('canceled_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image',
         'track_orders.created_at as placed_order_at', 'canceled_orders.created_at as canceled_order_at', 'products.image as image'
         ,'products.voltage as voltage', 'products.powerSources as powerSources', 'products.weight as weight', 'products.dimensions as dimensions', 
         'products.material as material', 'track_orders.total_price as total_price');
    
        if (!empty($brand)) {
            $query->where('products.brand', $brand);
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }
    
        $data = $query->get();

        // Format date strings
        $data->transform(function ($item) {
            $item->placed_order_at = $item->placed_order_at ? \Carbon\Carbon::parse($item->placed_order_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            $item->canceled_order_at = $item->canceled_order_at ? \Carbon\Carbon::parse($item->canceled_order_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });

        return response()->json(['data' => $data]);
    }
}
