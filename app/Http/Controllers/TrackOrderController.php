<?php

namespace App\Http\Controllers;

use App\Models\CanceledOrder;
use App\Models\Product;
use App\Models\SerialNumber;
use App\Models\TrackOrder;
use App\Models\User;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function index(){
        return view('admin.track-order');
    }

    public function show()
    {
        $data = TrackOrder::leftjoin('products', 'track_orders.product_id', '=', 'products.id')
            ->select('track_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image',
            'products.voltage as voltage', 'products.dimensions as dimensions', 'products.weight as weight', 'products.powerSources as powerSources')
            ->where('track_orders.is_canceled', false)
            ->where('track_orders.is_completed', false)
            ->get();

        return response()->json([ 'data' => $data, ]);
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
            $canceledOrder->save();
    
            //UPDATE THE SERIAL NUMBER DATA TO AVAILABLE
            $serialNumbers = json_decode($trackOrder->serial_numbers, true);
            // Iterate through each serial number and update its status to available
            foreach ($serialNumbers as $serialNumber) {
                $serial = SerialNumber::where('serial_number', $serialNumber)->first();
                if ($serial) {
                    $serial->is_selected = false;
                    $serial->save();
                }
            }
            // UPDATE THE STOCKS OF THE PRODUCT
            $product = Product::find($trackOrder->product_id);
            $product->stocks += count($serialNumbers);
            $product->save();
    
            // UPDATE THE BALANCE OF THE USER
            $userBalance = User::find($trackOrder->user_id);
            $userBalance->balance += $trackOrder->total_price;
            $userBalance->save();
            return response()->json(['status' => 'success', 'message' => 'Order has been Canceled']);
        }
        else{
            return response()->json(['status' => 'warning', 'message' => 'Order cannot be canceled because it is already '.$trackOrder['status']]);
        }
    }
    
    
    
}
