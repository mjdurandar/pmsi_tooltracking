<?php

namespace App\Http\Controllers;

use App\Models\CanceledOrder;
use App\Models\ToolsAndEquipment;
use App\Models\TrackOrder;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function index(){
        return view('admin.track-order');
    }

    public function show()
    {
        $data = TrackOrder::leftJoin('tools_and_equipment', function($join) {
            $join->on('tools_and_equipment.id', '=', 'track_orders.tools_and_equipment_id');
        })
        ->leftJoin('products', function($join) {
            $join->on('products.id', '=', 'tools_and_equipment.product_id');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'products.user_id');
        })
        ->leftJoin('serial_numbers', function($join) {
            $join->on('serial_numbers.id', '=', 'tools_and_equipment.serial_number_id');
        })
        ->select('track_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'serial_numbers.serial_number as serial_number',
            'products.image as image', 'users.name as supplier_name', 'users.location as supplier_location', 'products.voltage as voltage',
            'products.powerSources as powerSources', 'products.weight as weight', 'products.dimensions as dimensions', 'products.material as material')
        ->where('track_orders.is_canceled', false) 
        ->whereIn('track_orders.id', function($query) {
            $query->selectRaw('MIN(id)')
                  ->from('track_orders')
                  ->groupBy('created_at');
        })
        ->get();
    
    

        return response()->json([ 'data' => $data, ]);
    }

    public function cancelOrder(Request $request)
    {   

        $trackOrder = TrackOrder::findOrFail($request->id);
        if($trackOrder->status === 'Pending'){
            $trackOrder->is_canceled = true;
            $trackOrder->save();
    
            $canceledOrder = new CanceledOrder();
            $canceledOrder->track_order_id = $trackOrder->id;
            $canceledOrder->reason = $request->reason;
            $canceledOrder->save();
        
            return response()->json(['status' => 'success', 'message' => 'Order has been Canceled']);
        }
        
        return response()->json(['status' => 'error', 'message' => 'Order cannot be canceled']);
     
    }
}
