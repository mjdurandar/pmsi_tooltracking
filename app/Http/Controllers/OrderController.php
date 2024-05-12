<?php

namespace App\Http\Controllers;

use App\Models\Borrowed;
use App\Models\BorrowedProduct;
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
use App\Models\Receipts;
use App\Models\TrackOrder;
use App\Models\User;
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
                                ->leftjoin('ordered_products', 'ordered_products.id', 'completed_order_users.ordered_product_id')
                                ->leftJoin('products', 'products.id', 'track_orders.product_id')
                                ->leftJoin('users', 'users.id', 'track_orders.user_id')
                                ->select('completed_order_users.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
                                        ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
                                        'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status', 'track_orders.total_price as total_price'
                                        ,'users.location as location', 'users.contact_address as contact_address', 'users.email as email', 'users.name as user_name', 'track_orders.serial_numbers as serial_numbers',
                                        'completed_order_users.created_at as completed_at','track_orders.type as type','ordered_products.order_number as order_number')
                                ->get();

                                $data->transform(function ($item) {
                                    $item->completed_at = $item->completed_at ? \Carbon\Carbon::parse($item->completed_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                    return $item;
                                });

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
                                        'track_orders.type as type', 'track_orders.serial_numbers as serial_numbers', 'ordered_products.created_at as ordered_at'
                                        , DB::raw('LENGTH(track_orders.serial_numbers) - LENGTH(REPLACE(track_orders.serial_numbers, ",", "")) + 1 as serial_numbers_count'))
                                ->where('ordered_products.user_id', '!=', Auth::id())
                                ->where('track_orders.is_canceled', false)
                                ->where('track_orders.is_completed', false)
                                ->get();

                                $data->transform(function ($item) {
                                    $item->ordered_at = $item->ordered_at ? \Carbon\Carbon::parse($item->ordered_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                    return $item;
                                });

        return response()->json([ 'data' => $data ]);
    }

    public function updateStatus(Request $request) {

        $orderNumber = 'ORD-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        $orderedProducts = OrderedProducts::find($request->id);

        // Parse and format the shipment date
        $shipmentDate = Carbon::parse($request->shipment_date)->format('m/d/Y');
        $orderedProducts->shipment_date = $shipmentDate;
        
        // Parse and format the delivery date
        $deliveryDate = Carbon::parse($request->delivery_date)->format('m/d/Y');
        $orderedProducts->delivery_date = $deliveryDate;
        $orderedProducts->order_number = $orderNumber;
        $orderedProducts->save();

        $trackOrders = TrackOrder::find($orderedProducts->track_orders_id);
        $trackOrders->status = $request->status_data;
        $trackOrders->save();

        if($request->status_data == 'Completed'){
            $trackOrders->is_completed = true;
            $trackOrders->save();

            $completedOrder = new CompletedOrderUser();
            $completedOrder->track_order_id = $trackOrders->id;
            $completedOrder->ordered_product_id = $orderedProducts->id;
            $completedOrder->save();

            $userBalance = User::findOrFail(Auth::id());
            $userBalance->balance += $trackOrders->total_price;
            $userBalance->save();

            $receipts = new Receipts();
            $receipts->track_order_id = $request->track_orders_id;
            $receipts->user_id = $request->user_id;
            $receipts->ordered_product_id = $request->id;
            $receipts->total_price = $request->total_price;
            $receipts->receipt_number = 'REC-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
            $receipts->save();

            if($request->type === 'Borrowing')
            {
                $borrowed = BorrowedProduct::where('ordered_product_id', $orderedProducts->id)->first();
                $borrowed->is_delivered = true;
                $borrowed->save();
            }
        }

        return response()->json(['message' => 'Status updated successfully']);

    }

    public function filterData(Request $request){
        
        $orderNumber = $request->orderNumber;
        $selectedStatus = $request->selectedStatus;
        $selectedType = $request->selectedType;

        $query = OrderedProducts::query();
    
        $query->leftJoin('track_orders', 'track_orders.id', 'ordered_products.track_orders_id')
        ->leftJoin('products', 'products.id', 'track_orders.product_id')
        ->leftJoin('users', 'users.id', 'ordered_products.user_id')
        ->select('ordered_products.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
                ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
                'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status', 'track_orders.total_price as total_price'
                ,'users.location as location', 'users.contact_address as contact_address', 'users.email as email', 'users.name as user_name', 
                'track_orders.type as type', 'track_orders.serial_numbers as serial_numbers', 'ordered_products.created_at as ordered_at'
                , DB::raw('LENGTH(track_orders.serial_numbers) - LENGTH(REPLACE(track_orders.serial_numbers, ",", "")) + 1 as serial_numbers_count'))
        ->where('ordered_products.user_id', '!=', Auth::id())
        ->where('track_orders.is_canceled', false)
        ->where('track_orders.is_completed', false)
        ->get();

        if (!empty($orderNumber)) {
            $query->where('ordered_products.order_number', 'like', '%' . $orderNumber . '%');
        }        

        if (!empty($selectedStatus)) {
            $query->where('track_orders.status', $selectedStatus);
        }

        if (!empty($selectedType)) {
            $query->where('ordered_products.status', $selectedType);
        }
    
        $data = $query->get();

        $data->transform(function ($item) {
            $item->ordered_at = $item->ordered_at ? \Carbon\Carbon::parse($item->ordered_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });

        return response()->json(['data' => $data]);
    
    }    

    public function completedFilterData(Request $request)
    {
        $orderNumber = $request->orderNumber;
        $selectedType = $request->selectedType;

        $query = CompletedOrderUser::leftjoin('track_orders', 'track_orders.id', 'completed_order_users.track_order_id')
        ->leftjoin('ordered_products', 'ordered_products.id', 'completed_order_users.ordered_product_id')
        ->leftJoin('products', 'products.id', 'track_orders.product_id')
        ->leftJoin('users', 'users.id', 'track_orders.user_id')
        ->select('completed_order_users.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
                ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
                'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status', 'track_orders.total_price as total_price'
                ,'users.location as location', 'users.contact_address as contact_address', 'users.email as email', 'users.name as user_name', 'track_orders.serial_numbers as serial_numbers',
                'completed_order_users.created_at as completed_at','track_orders.type as type','ordered_products.order_number as order_number');

        if (!empty($orderNumber)) {
            $query->where('ordered_products.order_number', 'like', '%' . $orderNumber . '%');
        }        

        if (!empty($selectedType)) {
            $query->where('track_orders.type', $selectedType);
        }
    
        $data = $query->get();

        $data->transform(function ($item) {
            $item->completed_at = $item->completed_at ? \Carbon\Carbon::parse($item->completed_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });

        return response()->json(['data' => $data]);
    }

    public function canceledFilterData(Request $request)
    {
        $brand = $request->brand;
        $tool = $request->tool;
    
        $query = OrderedProducts::leftJoin('track_orders', 'track_orders.id', 'ordered_products.track_orders_id')
                            ->leftJoin('products', 'products.id', 'track_orders.product_id')
                            ->leftJoin('users', 'users.id', 'ordered_products.user_id')
                            ->select('ordered_products.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
                                    ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
                                    'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status', 'track_orders.total_price as total_price'
                                    ,'users.location as location', 'users.contact_address as contact_address', 'users.email as email', 'users.name as user_name', 
                                    'track_orders.type as type', 'track_orders.serial_numbers as serial_numbers', 'ordered_products.created_at as ordered_at'
                                    , DB::raw('LENGTH(track_orders.serial_numbers) - LENGTH(REPLACE(track_orders.serial_numbers, ",", "")) + 1 as serial_numbers_count'))
                            ->where('ordered_products.user_id', '!=', Auth::id())
                            ->where('track_orders.is_canceled', false)
                            ->where('track_orders.is_completed', false);
    
        if (!empty($brand)) {
            $query->where('products.brand', $brand);
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }
    
        $data = $query->get();

        $data->transform(function ($item) {
            $item->ordered_at = $item->ordered_at ? \Carbon\Carbon::parse($item->ordered_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });

        return response()->json(['data' => $data]);
    }

}
