<?php

namespace App\Http\Controllers;

use App\Models\AdminReleasedProducts;
use App\Models\Borrowed;
use App\Models\BorrowHistory;
use App\Models\CanceledOrder;
use App\Models\CanceledOrderUser;
use App\Models\CompletedOrderUser;
use Illuminate\Http\Request;
use App\Models\DeliverHistory;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Product;
use App\Models\PurchasedItems;
use App\Models\ReleasedProduct;
use App\Models\SerialNumber;
use App\Models\Sold;
use App\Models\ToolsAndEquipment;
use App\Models\TrackOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index() {
        return view('users.delivery');
    }

    public function completedOrderUserIndex() {
        return view('users.completed-order-user');
    }

    public function canceledOrderUserIndex() {
        return view('users.canceled-order-user');
    }

    public function completedOrderUserShow()
    {
        $data = CompletedOrderUser::leftjoin('track_orders', 'track_orders.id', 'completed_order_users.track_order_id')
                                ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                ->leftJoin('users', 'users.id', 'track_orders.user_id')
                                ->select('completed_order_users.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image', 
                                'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 
                                'products.material as material', 'completed_order_users.created_at as canceled_at', 'track_orders.created_at as ordered_at', 'track_orders.total_price as total_price'
                                ,'track_orders.serial_numbers as serial_numbers', 'completed_order_users.created_at as completed_at', 'users.location as location', 'users.contact_address as contact_address', 'users.email as email', 'users.name as user_name')
                                ->get();

                                $data->transform(function ($item) {
                                    $item->completed_at = $item->completed_at ? \Carbon\Carbon::parse($item->completed_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                    return $item;
                                });

        return response()->json(['data' => $data]);
    }


    public function canceledOrderUserShow()
    {
        $data = CanceledOrder::leftjoin('track_orders', 'track_orders.id', 'canceled_orders.track_order_id')
                                ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                ->select('canceled_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image', 
                                'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 
                                'products.material as material', 'canceled_orders.created_at as canceled_at', 'track_orders.created_at as ordered_at', 'track_orders.total_price as total_price')
                                ->where('canceled_orders.user_id', Auth::id())
                                ->get();

                                $data->transform(function ($item) {
                                    $item->ordered_at = $item->ordered_at ? \Carbon\Carbon::parse($item->ordered_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                    $item->canceled_at = $item->canceled_at ? \Carbon\Carbon::parse($item->canceled_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                    return $item;
                                });

        return response()->json(['data' => $data]);
    }

    public function show() {
        $id = Auth::id();

        $data = TrackOrder::leftjoin('products', 'products.id', 'track_orders.product_id')
                            ->select('track_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image', 
                            'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 'products.material as material', 'track_orders.created_at as ordered_at')
                            ->where('track_orders.user_id', $id)
                            ->where('track_orders.is_canceled', false)
                            ->where('track_orders.status', '!=', 'Completed')
                            ->get();

                            $data->transform(function ($item) {
                                $item->ordered_at = $item->ordered_at ? \Carbon\Carbon::parse($item->ordered_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                return $item;
                            });
                    

        return response()->json([ 'data' => $data ]);
    }

    public function filterData(Request $request) {
        $brand = $request->brand;
        $tool = $request->tool;
        
        $id = Auth::id();
    
        $query = TrackOrder::leftjoin('products', 'products.id', 'track_orders.product_id')
                ->select('track_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image', 
                'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 'products.material as material', 'track_orders.created_at as ordered_at')
                ->where('track_orders.user_id', $id)
                ->where('track_orders.is_canceled', false)
                ->where('track_orders.status', '!=', 'Completed');

        if (!empty($brand)) {
            $query->where('products.brand', 'like', '%' . $brand . '%');
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

    public function cancelOrder(Request $request)
    {   
        //UPDATE THE TRACK ORDER DATA TO CANCELED
        $trackOrder = TrackOrder::find($request->id);

        if($trackOrder->status == 'Pending'){   

            $trackOrder->status = 'Canceled';
            $trackOrder->is_canceled = true;
            $trackOrder->save();
    
            //ADD THE DATA OF CANCLED ORDER TO CANCELED ORDER TABLE
            $canceledOrder = new CanceledOrder();
            $canceledOrder->track_order_id = $trackOrder->id;
            $canceledOrder->reason = $request->reason;
            $canceledOrder->user_id = Auth::id();
            $canceledOrder->save();

            $adminReleaseProduct = AdminReleasedProducts::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'admin_released_products.tools_and_equipment_id')
                                                        ->where('tools_and_equipment.product_id', $request->product_id)->first();
                                           
            // Decode the JSON string of serial numbers stored in the database column
            $existingSerialNumbers = json_decode($adminReleaseProduct->serial_numbers, true);
            // Append the new serial numbers to the existing ones
            $newSerialNumbers = json_decode($request->serial_numbers);
            $updatedSerialNumbers = array_merge($existingSerialNumbers, $newSerialNumbers);
            
            // Update the serial_numbers column in the database with the updated array
            $adminReleaseProduct->serial_numbers = $updatedSerialNumbers;
            $adminReleaseProduct->save();
    
            // UPDATE THE BALANCE OF THE USER
            $userBalance = User::find(Auth::id());
            $userBalance->balance += $trackOrder->total_price;
            $userBalance->save();

            // dd($trackOrder, $canceledOrder, $adminReleaseProduct, $userBalance);
            return response()->json(['status' => 'success', 'message' => 'Order has been Canceled']);
        }
        else{
            return response()->json(['status' => 'warning', 'message' => 'Order cannot be canceled because it is already '.$trackOrder['status']]);
        }
    }

    public function completedFilterData(Request $request)
    {
        $brand = $request->brand;
        $tool = $request->tool;
    
        $query = CompletedOrderUser::leftjoin('track_orders', 'track_orders.id', 'completed_order_users.track_order_id')
        ->leftjoin('products', 'products.id', 'track_orders.product_id')
        ->leftJoin('users', 'users.id', 'track_orders.user_id')
        ->select('completed_order_users.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image', 
        'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 
        'products.material as material', 'completed_order_users.created_at as canceled_at', 'track_orders.created_at as ordered_at', 'track_orders.total_price as total_price'
        ,'track_orders.serial_numbers as serial_numbers', 'completed_order_users.created_at as completed_at', 'users.location as location',
        'users.contact_address as contact_address', 'users.email as email', 'users.name as user_name');

        if (!empty($brand)) {
            $query->where('products.brand', $brand);
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
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
    
        $query = CanceledOrder::leftjoin('track_orders', 'track_orders.id', 'canceled_orders.track_order_id')
        ->leftjoin('products', 'products.id', 'track_orders.product_id')
        ->select('canceled_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image', 
        'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 
        'products.material as material', 'canceled_orders.created_at as canceled_at', 'track_orders.created_at as ordered_at', 'track_orders.total_price as total_price')
        ->where('canceled_orders.user_id', Auth::id());

        if (!empty($brand)) {
            $query->where('products.brand', $brand);
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }

        $data = $query->get();

        $data->transform(function ($item) {
            $item->ordered_at = $item->ordered_at ? \Carbon\Carbon::parse($item->ordered_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            $item->canceled_at = $item->canceled_at ? \Carbon\Carbon::parse($item->canceled_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });

        return response()->json(['data' => $data]);
    }
}
