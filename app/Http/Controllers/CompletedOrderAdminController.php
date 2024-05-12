<?php

namespace App\Http\Controllers;

use App\Models\CompletedOrderAdmin;
use App\Models\TrackOrder;
use Illuminate\Http\Request;

class CompletedOrderAdminController extends Controller
{
    public function index()
    {
        return view('admin.completed-order-admin');
    }

    public function show()
    {
        $data = CompletedOrderAdmin::leftJoin('track_orders', 'completed_order_admins.track_order_id', 'track_orders.id')
            ->leftJoin('products', 'track_orders.product_id', 'products.id')
            ->leftJoin('users', 'track_orders.user_id', 'users.id')
            ->select(
                'completed_order_admins.*',
                'products.brand as brand_name',
                'products.tool as tool_name',
                'products.voltage as voltage',
                'track_orders.status as status',
                'completed_order_admins.created_at as ordered_at',
                'users.name as user_name',
                'users.location as location',
                'products.image as image',
                'products.dimensions as dimensions',
                'products.weight as weight',
                'products.powerSources as powerSources',
                'track_orders.total_price as total_price',
                'users.location as location',
                'track_orders.serial_numbers as serial_numbers',
            )
            ->where('track_orders.is_completed', true)
            ->get();

            foreach ($data as $order) {
                $order->serial_numbers = json_decode($order->serial_numbers);
            }

            $data->transform(function ($item) {
                $item->ordered_at = $item->ordered_at ? \Carbon\Carbon::parse($item->ordered_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                return $item;
            });

        return response()->json([ 'data' => $data, ]);
    }

    public function filterData(Request $request)
    {
        $brand = $request->brand;
        $tool = $request->tool;
    
        $query = CompletedOrderAdmin::leftJoin('track_orders', 'completed_order_admins.track_order_id', 'track_orders.id')
        ->leftJoin('products', 'track_orders.product_id', 'products.id')
        ->leftJoin('users', 'track_orders.user_id', 'users.id')
        ->select(
            'completed_order_admins.*',
            'products.brand as brand_name',
            'products.tool as tool_name',
            'products.voltage as voltage',
            'track_orders.status as status',
            'completed_order_admins.created_at as ordered_at',
            'users.name as user_name',
            'users.location as location',
            'products.image as image',
            'products.dimensions as dimensions',
            'products.weight as weight',
            'products.powerSources as powerSources',
            'track_orders.total_price as total_price',
            'users.location as location',
            'track_orders.serial_numbers as serial_numbers',
        )
        ->where('track_orders.is_completed', true);
    
        if (!empty($brand)) {
            $query->where('products.brand', $brand);
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }
    
        $data = $query->get();

        foreach ($data as $item) {
            $item->serial_numbers = json_decode($item->serial_numbers);
        }

        $data->transform(function ($item) {
            $item->ordered_at = $item->ordered_at ? \Carbon\Carbon::parse($item->ordered_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });

        return response()->json(['data' => $data]);
    }
}
