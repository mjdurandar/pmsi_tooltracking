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

        //NOTIFY FOR ORDERS SUPPLIER
        $notifications = Notification::leftjoin('track_orders', 'track_orders.id', 'notifications.track_order_id')
                                    ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                    ->where('products.user_id', $user->id)
                                    ->where('notifications.is_done', false)
                                    ->where('notifications.is_canceled', false)
                                    ->select('notifications.description')
                                    ->get();

        //NOTIFY IF ALREADY BEEN DELIVERED ADMIN
        $completedOrder = Notification::leftjoin('track_orders', 'track_orders.id', 'notifications.track_order_id')
                                    ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                    ->where('track_orders.user_id', $user->id)
                                    ->where('notifications.is_done', true)
                                    ->where('notifications.is_canceled', false)
                                    ->where('notifications.is_approved', false)
                                    ->select('notifications.*')
                                    ->get();

        //NOTIFY IF PRODUCT IS RETURNED SUPPLIER
        $returnedProducts = Notification::leftjoin('track_orders', 'track_orders.id', 'notifications.track_order_id')
                                    ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                    ->where('products.user_id', $user->id)
                                    ->where('notifications.is_done', true)
                                    ->where('notifications.is_canceled', false)
                                    ->where('notifications.is_returned', true)
                                    ->where('notifications.is_approved', false)
                                    ->select('notifications.*')
                                    ->get();

        //NOTIFY FOR ORDERS ADMIN
        $notifyAdminforOrders = Notification::leftjoin('track_orders', 'track_orders.id', 'notifications.track_order_id')
                                    ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                    ->where('track_orders.user_id', 2)
                                    ->where('notifications.is_done', false)
                                    ->where('notifications.is_canceled', false)
                                    ->where('notifications.is_approved', false)
                                    ->select('notifications.*')
                                    ->get();
   
        return view('home', compact('role', 'notifications', 'completedOrder', 'returnedProducts', 'notifyAdminforOrders'));
    }

}
