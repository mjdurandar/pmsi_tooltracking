<?php

namespace App\Http\Controllers;

use App\Models\AdminReturnedProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnedProductsSupplierController extends Controller
{
    public function index()
    {
        return view('supplier.returned-products-supplier');
    }

    public function show()
    {
        $data = AdminReturnedProducts::leftjoin('track_orders', 'track_orders.id', 'admin_returned_products.track_order_id')
                                    ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                    ->leftjoin('users', 'users.id', 'track_orders.user_id')
                                    ->leftjoin('users as supplier', 'supplier.id', 'products.user_id')
                                    ->select('admin_returned_products.*', 'products.brand as brand_name', 'products.tool as tool_name' ,'products.image as image',
                                    'admin_returned_products.created_at as returned_at', 'users.name as user_name', 'products.voltage as voltage'
                                    ,'products.weight as weight', 'products.powerSources as powerSources', 'products.dimensions as dimensions', 'users.contact_address as user_phone',
                                    'users.email as user_email', 'users.location as location')
                                    ->where('products.user_id', Auth::id())
                                    ->get();

                                    $data->transform(function ($item) {
                                        $item->returned_at = $item->returned_at ? \Carbon\Carbon::parse($item->returned_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                        return $item;
                                    });

        return response()->json([ 'data' => $data ]);
    }
}
