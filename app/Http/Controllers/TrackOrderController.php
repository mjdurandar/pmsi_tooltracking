<?php

namespace App\Http\Controllers;

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
        $data = TrackOrder::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'track_orders.tools_and_equipment_id')
                        ->leftJoin('products', 'products.id', 'tools_and_equipment.product_id')
                        ->leftJoin('users', 'users.id', 'products.user_id')
                        ->leftJoin('serial_numbers', 'serial_numbers.id', 'tools_and_equipment.serial_number_id')
                        ->select('track_orders.*', 'products.brand as brand_name', 'products.tool as tool_name','serial_numbers.serial_number as serial_number',
                        'products.image as image', 'users.name as supplier_name', 'users.location as supplier_location')
                        ->get();

        return response()->json([ 'data' => $data, ]);
    }

    public function cancelOrder(Request $request)
    {   

        dd($request->all());
        // $order = Order::where('tools_and_equipment_id', $delivery->tools_and_equipment_id)->first();
       
        // if($order->status === 'Preparing') {
        //     $toolsandEquipmentId = ToolsAndEquipment::findOrFail($delivery->tools_and_equipment_id);
        //     $toolsandEquipmentId->status = 'For Sale';
        //     $toolsandEquipmentId->save();

        //     //UPDATE BALANCE
        //     $user = Auth::user();
        //     $newBalance = User::where('id', $user->id)->first();
        //     $newBalance->balance = $user->balance + $toolsandEquipmentId->price;
        //     $newBalance->save();

        //     //UPDATE STATUS
        //     $toolsandEquipmentId->status = 'For Sale';
        //     $toolsandEquipmentId->save();

        //     $borrowed = Borrowed::where('tools_and_equipment_id', $toolsandEquipmentId->id)->first();
        //     if($borrowed){
        //         $borrowed->delete();
        //     }

        //     $sold = Sold::where('tools_and_equipment_id', $toolsandEquipmentId->id)->first();
            
        //     $purchasedItems = PurchasedItems::where('tools_and_equipment_id', $toolsandEquipmentId->id)->first();

        //     $purchasedItems->delete();
        //     $sold->delete();
        //     $order->delete();
        //     $delivery->delete();
            
        //     return response()->json(['status' => 'success', 'message' => 'Order has been Canceled']);
        // }
        // else {
        //     return response()->json(['status' => 'warning', 'message' => 'Order is already ' . $order->status]);
        // }
    }
}
