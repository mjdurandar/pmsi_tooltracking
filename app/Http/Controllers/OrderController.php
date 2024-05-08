<?php

namespace App\Http\Controllers;

use App\Models\Borrowed;
use App\Models\CanceledOrder;
use App\Models\CompletedOrderAdmin;
use App\Models\CompletedOrderUser;
use App\Models\CustomerOrderedProducts;
use App\Models\DeliverHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderedProducts;
use App\Models\PurchasedItems;
use App\Models\TrackOrder;
use App\Models\UserDelivery;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index() {
        return view('admin.order');
    }

    public function completeOrderAdminIndex()
    {
        return view('admin.complete-order-admin-product');
    }

    public function canceledOrderAdminIndex()
    {
        return view('admin.canceled-order-admin-product');
    }

    public function completeOrderAdminShow()
    {
        $data = CompletedOrderUser::leftjoin('track_orders', 'track_orders.id', 'completed_order_users.track_order_id')
                                ->leftJoin('products', 'products.id', 'track_orders.product_id')
                                ->leftJoin('users', 'users.id', 'track_orders.user_id')
                                ->select('completed_order_users.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
                                        ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
                                        'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status', 'track_orders.total_price as total_price'
                                        ,'users.location as location', 'users.contact_address as contact_address', 'users.email as email', 'users.name as user_name', 'track_orders.serial_numbers as serial_numbers',
                                        'completed_order_users.created_at as completed_at')
                                ->get();

                                $data->transform(function ($item) {
                                    $item->completed_at = $item->completed_at ? \Carbon\Carbon::parse($item->completed_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                    return $item;
                                });
        // dd($data);
        return response()->json(['data' => $data]);
    }

    public function canceledOrderAdminShow()
    {
        $data = CanceledOrder::leftjoin('track_orders', 'track_orders.id', 'canceled_orders.track_order_id')
                            ->leftJoin('products', 'products.id', 'track_orders.product_id')
                            ->leftJoin('users', 'users.id', 'track_orders.user_id')
                            ->select('canceled_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
                                    ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
                                    'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status', 'track_orders.total_price as total_price'
                                    ,'users.location as location', 'users.contact_address as contact_address', 'users.email as email', 'users.name as user_name', 'track_orders.serial_numbers as serial_numbers',
                                    'canceled_orders.created_at as canceled_at', 'track_orders.created_at as ordered_at')
                            ->where('canceled_orders.user_id', '!=', Auth::id())
                            ->get();

                            $data->transform(function ($item) {
                                $item->canceled_at = $item->canceled_at ? \Carbon\Carbon::parse($item->canceled_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                $item->ordered_at = $item->ordered_at ? \Carbon\Carbon::parse($item->ordered_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                return $item;
                            });

        return response()->json(['data' => $data]);
    }

    public function show () { 

        $data = OrderedProducts::leftJoin('track_orders', 'track_orders.id', 'ordered_products.track_orders_id')
                                ->leftJoin('products', 'products.id', 'track_orders.product_id')
                                ->leftJoin('users', 'users.id', 'ordered_products.user_id')
                                ->select('ordered_products.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
                                        ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
                                        'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status', 'track_orders.total_price as total_price'
                                        ,'users.location as location', 'users.contact_address as contact_address', 'users.email as email', 'users.name as user_name', 
                                        'track_orders.type as type', 'track_orders.serial_numbers as serial_numbers' 
                                        , DB::raw('LENGTH(track_orders.serial_numbers) - LENGTH(REPLACE(track_orders.serial_numbers, ",", "")) + 1 as serial_numbers_count'))
                                ->where('ordered_products.user_id', '!=', Auth::id())
                                ->where('track_orders.is_canceled', false)
                                ->where('track_orders.is_completed', false)
                                ->get();

                                $data->transform(function ($item) {
                                    $item->created_at = $item->created_at ? \Carbon\Carbon::parse($item->created_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                    return $item;
                                });

        return response()->json([ 'data' => $data ]);
    }

    public function updateStatus(Request $request) {
        
        $orderedProducts = OrderedProducts::find($request->id);
        // Parse and format the shipment date
        $shipmentDate = Carbon::parse($request->shipment_date)->format('m/d/Y');
        $orderedProducts->shipment_date = $shipmentDate;
        
        // Parse and format the delivery date
        $deliveryDate = Carbon::parse($request->delivery_date)->format('m/d/Y');
        $orderedProducts->delivery_date = $deliveryDate;
        $orderedProducts->save();

        $trackOrders = TrackOrder::find($orderedProducts->track_orders_id);
        $trackOrders->status = $request->status_data;
        $trackOrders->save();

        if($request->status_data == 'Completed'){
            $trackOrders->is_completed = true;
            $trackOrders->save();

            $completedOrder = new CompletedOrderUser();
            $completedOrder->track_order_id = $trackOrders->id;
            $completedOrder->save();
        }

        return response()->json(['message' => 'Status updated successfully']);

    }
}
