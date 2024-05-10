<?php

namespace App\Http\Controllers;

use App\Models\BorrowedProduct;
use Illuminate\Http\Request;

class ReturningProductsController extends Controller
{
    public function index()
    {
        return view('admin.returning-products');
    }

    public function show()
    {
        $data = BorrowedProduct::leftJoin('ordered_products', 'borrowed_products.ordered_product_id', 'ordered_products.id')
        ->leftJoin('users', 'users.id', 'ordered_products.user_id')
        ->leftJoin('track_orders', 'track_orders.id', 'ordered_products.track_orders_id')
        ->leftjoin('products', 'products.id', 'track_orders.product_id')
        ->select('borrowed_products.*','products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
        ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
        'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status' ,'track_orders.total_price as total_price'
        ,'track_orders.serial_numbers as serial_numbers', 'users.name as user_name', 'users.email as user_email', 'users.contact_address as contact_address', 'users.location as location')
        ->where('borrowed_products.is_returned', true)
        ->get();

        return response()->json([ 'data' => $data ]);   
        
    }
}
