<?php

namespace App\Http\Controllers;

use App\Models\AdminReleasedProducts;
use App\Models\BorrowTools;
use Illuminate\Http\Request;
use App\Models\DeliverHistory;
use Carbon\Carbon;
use App\Models\Borrowed;
use App\Models\BorrowedProduct;
use App\Models\History;
use App\Models\Order;
use App\Models\OrderedProducts;
use App\Models\PurchasedItems;
use App\Models\ReturnDays;
use App\Models\Sales;
use App\Models\Sold;
use App\Models\ToolsAndEquipment;
use App\Models\TrackOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowToolsController extends Controller
{
    public function index()
    {
        return view('users.borrowtools');
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
                                    ->where('status', 'For Borrowing')
                                    ->where('admin_released_products.serial_numbers', '!=', '[]')
                                    ->get();

                                    foreach ($data as $order) {
                                        $order->serial_numbers = json_decode($order->serial_numbers);
                                    }

        $returndays = ReturnDays::get();

        return response()->json([ 'data' => $data, 'returndays' => $returndays, 'userLocation' => $userLocation]);
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
        ->where('status', 'For Borrowing')
        ->where('admin_released_products.serial_numbers', '!=', '[]')
        ->get();
    
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
        
        return response()->json(['data' => $data]);
    }

    public function borrowTools(Request $request) {
        $totalPriceWithoutCurrency = str_replace('₱', '', $request->total_price);
        $totalPriceNumeric = floatval($totalPriceWithoutCurrency);
        $trackingNumber = 'TRK-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        $orderNumber = 'ORD-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        $historyNumber = 'HIS-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
    
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $remaining_balance = $user->balance - $totalPriceNumeric;
    
        if ($remaining_balance < 0) {
            return response()->json(['error' => 'Insufficient funds'], 400);
        }
    
        $user->balance = $remaining_balance;
        $user->save();
    
        foreach ($request->selectedProducts as $selectedProduct) {
            // Find all AdminReleasedProducts for the given tools_and_equipment_id
            $adminReleasedProducts = AdminReleasedProducts::where('tools_and_equipment_id', $selectedProduct['dataValues']['tools_and_equipment_id'])->get();
            // Iterate through each AdminReleasedProduct
            foreach ($adminReleasedProducts as $adminReleasedProduct) {
                // Decode the JSON string of serial numbers stored in the database column and cast it as an array
                $releasedSerialNumbers = (array) json_decode($adminReleasedProduct->serial_numbers);
                // Iterate through the selected serial numbers
                foreach ($selectedProduct['selectedSerialNumbers'] as $selectedSerialNumber) {
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
            $trackOrder->product_id = $selectedProduct['dataValues']['product_id'];
            $trackOrder->serial_numbers = json_encode($selectedProduct['selectedSerialNumbers']);
            $trackOrder->tracking_number = $trackingNumber;
            $trackOrder->type = "Borrowing";
            $trackOrder->total_price = $totalPriceNumeric;
            $trackOrder->user_id = $user_id;
            $trackOrder->save();
    
            // Create a new ordered product
            $orderedProduct = new OrderedProducts();
            $orderedProduct->user_id = $user_id;
            $orderedProduct->track_orders_id = $trackOrder->id;
            $orderedProduct->order_number = $orderNumber;
            $orderedProduct->status = 'Borrowing';
            $orderedProduct->shipment_date = '00/00/0000'; // Set the default shipment date
            $orderedProduct->delivery_date = '00/00/0000'; // Set the default delivery date
            $orderedProduct->save();
    
            // Create a borrowed product record
            $borrowed = new BorrowedProduct();
            $borrowed->ordered_product_id = $orderedProduct->id;
            $borrowed->return_date = $selectedProduct['dataValues']['return_date'];
            $borrowed->penalty = $selectedProduct['dataValues']['penalty'];
            $borrowed->save();
    
            // Create a history record
            $history = new History();
            $history->user_id = Auth::id();
            $history->history_number = $historyNumber;
            $history->product_id = $selectedProduct['dataValues']['product_id'];
            $history->action = 'You Borrow this Product and need to return at ' . $selectedProduct['dataValues']['return_date'];
            $history->save();
        }
    
        return response()->json(['message' => 'Data Successfully Saved']);
    }
    
}
