<?php

namespace App\Http\Controllers;

use App\Models\AdminReleasedProducts;
use App\Models\BuyTools;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CustomerHistory;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\User;
use App\Models\DeliverHistory;
use App\Models\Order;
use App\Models\PurchasedItems;
use App\Models\Sold;
use App\Models\ToolsAndEquipment;
use Illuminate\Support\Facades\Auth;

class BuyToolsController extends Controller
{
    public function index()
    {
        return view('users.buytools');
    }

    public function history() {
        return view('users.buyinghistory');
    }

    public function show(){

        $data = AdminReleasedProducts::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'admin_released_products.tools_and_equipment_id')    
                                    ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                                    ->select('admin_released_products.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as product_image',
                                    'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 'products.material as material')
                                    ->where('status', 'For Sale')
                                    ->get();

                                    foreach ($data as $order) {
                                        $order->serial_numbers = json_decode($order->serial_numbers);
                                    }
                            

        // ToolsAndEquipment::leftjoin('products', 'products.id', '=', 'tools_and_equipment.product_id')
        //         ->select('tools_and_equipment.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as product_image',
        //          'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 'products.material as material')
        //         ->where('category_id', 2)
        //         ->get();

        return response()->json([ 'data' => $data]);
    }

    public function store(Request $request)
    {
        // Get the authenticated user's ID
        $user_id = Auth::id();
    
        // Find the user record
        $user = User::findOrFail($user_id);
    
        // Calculate the remaining balance after deducting the requested balance
        $remaining_balance = $user->balance - $request->price;
    
        // Check if the remaining balance is sufficient
        if ($remaining_balance >= 0) {
            // Update the user's balance
            $user->balance = $remaining_balance;
            $user->save();
    
            // Proceed with the rest of the operations
            $sold = new Sold();
            $sold->user_id = $user_id;
            $sold->tools_and_equipment_id = $request->id;
            $sold->sold_at = now();
            $sold->type = 'Buying';
            $sold->save();
    
            $tools = ToolsAndEquipment::findOrFail($request->id);
            $tools->status = 'Sold';
            $tools->save();
    
            $customer_history = new CustomerHistory();
            $customer_history->user_id = $user_id;
            $customer_history->tools_and_equipment_id = $request->id;
            $customer_history->save();
    
            $delivery = new DeliverHistory();
            $delivery->user_id = $user_id;
            $delivery->tools_and_equipment_id = $request->id;
            $delivery->status = 'Preparing';
            $delivery->type = 'Sold';
            $delivery->save();
    
            $orders = new Order();
            $orders->user_id = $user_id;
            $orders->tools_and_equipment_id = $request->id;
            $orders->status = 'Preparing';
            $orders->type = 'Buying';
            $orders->shipment_date = '0000-00-00';
            $orders->delivery_date = '0000-00-00';
            $orders->save();

            $purchasedItems = new PurchasedItems();
            $purchasedItems->user_id = $user_id;
            $purchasedItems->tools_and_equipment_id = $request->id;
            $purchasedItems->status = 'Bought';
            $purchasedItems->save();
    
            return response()->json(['message' => 'Data Successfully Saved']);
        } else {
            // Return response indicating insufficient funds
            return response()->json(['error' => 'Insufficient funds'], 400);
        }
    }
    
    public function filterData(Request $request)
    {
        $status = $request->status;
        $brand = $request->brand;
        $tool = $request->tool;
        $specs = $request->specs;

        $query = ToolsAndEquipment::query();

        $query->leftJoin('products', 'products.id', '=', 'tools_and_equipment.product_id')
        ->select('tools_and_equipment.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as product_image',
                 'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 'products.material as material')
                 ->where('category_id', 2);
    
        if (!empty($brand)) {
            $query->where('products.brand', 'like', '%' . $brand . '%');
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }
    
        if (!empty($specs)) {
            $query->where('products.powerSources', 'like', '%' . $specs . '%');
        }

        if (!empty($status)) {
            $query->where('tools_and_equipment.status', 'like', '%' . $status . '%');
        }

        $data = $query->get();
        
        return response()->json(['data' => $data]);
    }
}
