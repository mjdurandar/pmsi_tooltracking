<?php

namespace App\Http\Controllers;

use App\Models\AdminReleasedProducts;
use App\Models\BuyTools;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CustomerHistory;
use App\Models\CustomerOrderedProducts;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\User;
use App\Models\DeliverHistory;
use App\Models\History;
use App\Models\Order;
use App\Models\OrderedProducts;
use App\Models\PurchasedItems;
use App\Models\Sales;
use App\Models\Sold;
use App\Models\ToolsAndEquipment;
use App\Models\TrackOrder;
use App\Models\UserDelivery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $user_id = Auth::id();
        $userName = User::findOrFail($user_id);
        $userLocation = $userName->location;

        $data = AdminReleasedProducts::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'admin_released_products.tools_and_equipment_id')    
                                    ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                                    ->select('admin_released_products.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as product_image',
                                    'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions',
                                    'products.material as material', 'tools_and_equipment.product_id as product_id', DB::raw('JSON_LENGTH(admin_released_products.serial_numbers) as stocks'))
                                    ->where('status', 'For Sale')
                                    ->where('admin_released_products.serial_numbers', '!=', '[]')
                                    ->get();

                                    foreach ($data as $order) {
                                        $order->serial_numbers = json_decode($order->serial_numbers);
                                    }

        return response()->json([ 'data' => $data, 'userLocation' => $userLocation]);
    }

    public function buyTools(Request $request)
    {   
        // Get the authenticated user's ID
        $user_id = Auth::id();
        // Find the user record
        $user = User::findOrFail($user_id);
        // Calculate the remaining balance after deducting the requested balance
        $remaining_balance = $user->balance - $request->total_price;
        // Check if the remaining balance is sufficient
        if ($remaining_balance > 0) {
            // Update the user's balance
            $user->balance = $remaining_balance;
            $user->save();
    
            // Find all AdminReleasedProducts for the given tools_and_equipment_id
            $adminReleasedProducts = AdminReleasedProducts::where('tools_and_equipment_id', $request->dataValues['tools_and_equipment_id'])->get();

            // Iterate through each AdminReleasedProduct
            foreach ($adminReleasedProducts as $adminReleasedProduct) {
                // Decode the JSON string of serial numbers stored in the database column
                $releasedSerialNumbers = json_decode($adminReleasedProduct->serial_numbers, true); // Convert to array

                // Iterate through the selected serial numbers
                foreach ($request->serial_numbers as $selectedSerialNumber) {
                    // Check if the selected serial number exists in the released serial numbers
                    if (($key = array_search($selectedSerialNumber, $releasedSerialNumbers)) !== false) {
                        // Remove the selected serial number from the released serial numbers
                        unset($releasedSerialNumbers[$key]);
                    }
                }

                // Update the serial_numbers column in the database with the updated array
                $adminReleasedProduct->serial_numbers = json_encode($releasedSerialNumbers);
                $adminReleasedProduct->save();
            }
                
            // Create a new track order
            $trackOrder = new TrackOrder();
            $trackOrder->status = 'Pending';
            $trackOrder->product_id = $request->dataValues['product_id'];
            $trackOrder->serial_numbers = json_encode($request->serial_numbers);
            $trackOrder->type = "Buying";
            $trackOrder->total_price = $request->total_price;
            $trackOrder->user_id = $user_id;
            $trackOrder->save();
    
            // Create a new ordered product
            $orderedProduct = new OrderedProducts();
            $orderedProduct->user_id = $user_id;
            $orderedProduct->track_orders_id = $trackOrder->id;
            $orderedProduct->status = 'Selling';
            $orderedProduct->shipment_date = '00/00/0000'; // Set the default shipment date
            $orderedProduct->delivery_date = '00/00/0000'; // Set the default delivery date
            $orderedProduct->save();

            $history = new History();
            $history->user_id = Auth::id();
            $history->product_id = $request->dataValues['product_id'];
            $history->action = 'You bought this Product at the price of ₱' . $request->total_price . ' including VAT';
            $history->save();

            $sales = new Sales();
            $sales->users_id = 1;
            $sales->total_price = $request->total_price;
            $sales->save();
    
            return response()->json(['message' => 'Product Ordered Successfully']);
        } else {
            // Return response indicating insufficient funds
            return response()->json(['error' => 'Insufficient funds'], 400);
        }
    }
    
    public function filterData(Request $request)
    {
        $brand = $request->brand;
        $tool = $request->tool;
        $specs = $request->specs;

        $query = AdminReleasedProducts::query();

        $query->leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'admin_released_products.tools_and_equipment_id')    
        ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
        ->select('admin_released_products.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as product_image',
        'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions',
        'products.material as material', 'tools_and_equipment.product_id as product_id', DB::raw('JSON_LENGTH(admin_released_products.serial_numbers) as stocks'))
        ->where('status', 'For Sale')
        ->where('admin_released_products.serial_numbers', '!=', '[]');
    
        if (!empty($brand)) {
            $query->where('products.brand', 'like', '%' . $brand . '%');
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }
    
        if (!empty($specs)) {
            $query->where('products.powerSources', 'like', '%' . $specs . '%');
        }
        

        $data = $query->get();

        foreach ($data as $order) {
            $order->serial_numbers = json_decode($order->serial_numbers);
        }

        // dd($data);
        
        return response()->json(['data' => $data]);
    }
}
