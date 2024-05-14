<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\OrderedProducts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {   
        $user = Auth::user();
        $role  = $user->role;

        //NOTIFY FOR ORDERS
        $notifications = Notification::leftjoin('track_orders', 'track_orders.id', 'notifications.track_order_id')
                                    ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                    ->where('products.user_id', $user->id)
                                    ->where('notifications.is_done', false)
                                    ->where('notifications.is_canceled', false)
                                    ->select('notifications.description')
                                    ->get();

        //NOTIFY IF ALREAY BEEND DELIVERED
        $completedOrder = Notification::leftjoin('track_orders', 'track_orders.id', 'notifications.track_order_id')
                                    ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                    ->where('track_orders.user_id', $user->id)
                                    ->where('notifications.is_done', true)
                                    ->where('notifications.is_canceled', false)
                                    ->where('notifications.is_approved', false)
                                    ->select('notifications.*')
                                    ->get();

        //NOTIFY IF PRODUCT IS RETURNED
        $returnedProducts = Notification::leftjoin('track_orders', 'track_orders.id', 'notifications.track_order_id')
                                    ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                    ->where('products.user_id', $user->id)
                                    ->where('notifications.is_done', true)
                                    ->where('notifications.is_canceled', false)
                                    ->where('notifications.is_returned', true)
                                    ->where('notifications.is_approved', false)
                                    ->select('notifications.*')
                                    ->get();

        //ORDERED PRODUCTS
        // $orderedProducts = OrderedProducts::leftJoin('track_orders', 'track_orders.id', 'ordered_products.track_orders_id')
        //                 ->leftJoin('products', 'products.id', 'track_orders.product_id')
        //                 ->leftJoin('users', 'users.id', 'ordered_products.user_id')
        //                 ->select('ordered_products.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
        //                         ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
        //                         'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status', 'track_orders.total_price as total_price'
        //                         ,'users.location as location', 'users.contact_address as contact_address', 'users.email as email', 'users.name as user_name', 
        //                         'track_orders.type as type', 'track_orders.serial_numbers as serial_numbers', 'ordered_products.created_at as ordered_at')
        //                 ->where('ordered_products.user_id', '!=', Auth::id())
        //                 ->where('track_orders.is_canceled', false)
        //                 ->where('track_orders.is_completed', false)
        //                 ->get();
   
        return view('home', compact('role', 'notifications', 'completedOrder', 'returnedProducts'));
    }

}
