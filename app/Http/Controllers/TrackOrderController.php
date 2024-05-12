<?php

namespace App\Http\Controllers;

use App\Models\CanceledOrder;
use App\Models\Product;
use App\Models\ReleasedProduct;
use App\Models\SerialNumber;
use App\Models\TrackOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                        ->where('track_orders.user_id', '=', Auth::id())
                        ->get();

        // Format serial numbers
        foreach ($data as $item) {
            $item->serial_numbers = json_decode($item->serial_numbers);
        }

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

            // $releasedProduct = ReleasedProduct::findOrFail($trackOrder->product_id);
            // $releasedProduct->is_sold = false;
            // $releasedProduct->save();

            // UPDATE THE STOCKS OF THE PRODUCT
            $product = Product::find($trackOrder->product_id);
            $product->stocks += count($serialNumbers);
            $product->save();
    
            // UPDATE THE BALANCE OF THE USER
            $userBalance = User::find(Auth::id());
            $userBalance->balance += $trackOrder->total_price;
            $userBalance->save();
            return response()->json(['status' => 'success', 'message' => 'Order has been Canceled']);
        }
        else{
            return response()->json(['status' => 'warning', 'message' => 'Order cannot be canceled because it is already '.$trackOrder['status']]);
        }
    }

    public function filterData(Request $request)
    {
        $brand = $request->brand;
        $tool = $request->tool;
        $status = $request->status;
        $trackingNumber = $request->trackingNumber;
    
        $query = TrackOrder::leftjoin('products', 'track_orders.product_id', '=', 'products.id')
        ->select('track_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image',
        'products.voltage as voltage', 'products.dimensions as dimensions', 'products.weight as weight', 'products.powerSources as powerSources')
        ->where('track_orders.is_canceled', false)
        ->where('track_orders.is_completed', false)
        ->where('track_orders.user_id', '=', Auth::id());
    
        if (!empty($brand)) {
            $query->where('products.brand', $brand);
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }
    
        if (!empty($status)) {
            $query->where('track_orders.status', 'like', '%' . $status . '%');
        }
    
        if (!empty($trackingNumber)) {
            $query->where('track_orders.tracking_number', 'like', '%' . $trackingNumber . '%');
        }

        $data = $query->get();

        foreach ($data as $item) {
            $item->serial_numbers = json_decode($item->serial_numbers);
        }

        return response()->json(['data' => $data]);
    }
    
}
