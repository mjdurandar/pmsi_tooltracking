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
                                    'products.material as material', 'tools_and_equipment.product_id as product_id')
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
    
        $query = ToolsAndEquipment::query();

        $query->leftJoin('products', 'products.id', '=', 'tools_and_equipment.product_id')
        ->select('tools_and_equipment.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as product_image',
                 'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 'products.material as material')
                 ->where('category_id', 3);
    
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
        
        return response()->json(['data' => $data]);
    }

    public function borrowTools(Request $request) {
        $user_id = Auth::id();
        // Find the user record
        $user = User::findOrFail($user_id);
        $remaining_balance = $user->balance - $request->price;
        
        if ($remaining_balance > 0) {

            $user->balance = $remaining_balance;
            $user->save();

            // Find all AdminReleasedProducts for the given tools_and_equipment_id
            $adminReleasedProducts = AdminReleasedProducts::where('tools_and_equipment_id', $request->dataValues['tools_and_equipment_id'])->get();

            // Iterate through each AdminReleasedProduct
            foreach ($adminReleasedProducts as $adminReleasedProduct) {
                // Decode the JSON string of serial numbers stored in the database column and cast it as an array
                $releasedSerialNumbers = (array) json_decode($adminReleasedProduct->serial_numbers);

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
            $trackOrder->type = "Borrowing";
            $trackOrder->total_price = $request->dataValues['price'];
            $trackOrder->user_id = $user_id;
            $trackOrder->save();
            // Create a new ordered product
            $orderedProduct = new OrderedProducts();
            $orderedProduct->user_id = $user_id;
            $orderedProduct->track_orders_id = $trackOrder->id;
            $orderedProduct->status = 'Borrowing';
            $orderedProduct->shipment_date = '00/00/0000'; // Set the default shipment date
            $orderedProduct->delivery_date = '00/00/0000'; // Set the default delivery date
            $orderedProduct->save();

            //CREATA A BORROWED PRODUCT DATA
            $borrowed = new BorrowedProduct();
            $borrowed->ordered_product_id = $orderedProduct->id;
            $borrowed->return_date = $request->dataValues['return_date'];
            $borrowed->penalty = $request->dataValues['penalty'];
            $borrowed->save();

            $history = new History();
            $history->user_id = Auth::id();
            $history->product_id = $request->dataValues['product_id'];
            $history->action = 'You Borrow this Product and need to return at ' . $request->dataValues['return_date'];
            $history->save();

            $sales = new Sales();
            $sales->users_id = 1;
            $sales->total_price = $request->dataValues['price'];
            $sales->save();

            return response()->json(['message' => 'Data Successfully Saved']);
        } else {
            // Return response indicating insufficient funds
            return response()->json(['error' => 'Insufficient funds'], 400);
        }
    }
}
