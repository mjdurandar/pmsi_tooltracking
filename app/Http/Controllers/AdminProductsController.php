<?php

namespace App\Http\Controllers;

use App\Models\AdminReturnedProducts;
use App\Models\History;
use App\Models\Product;
use App\Models\SerialNumber;
use App\Models\ToolsAndEquipment;
use App\Models\TrackOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminProductsController extends Controller
{
    public function index()
    {
        return view('admin.admin-products');
    }

    public function returnedProductIndex()
    {
        return view('admin.admin-returned-product');
    }

    public function returnedProductShow()
    {
        $data = AdminReturnedProducts::leftjoin('track_orders', 'track_orders.id', 'admin_returned_products.track_order_id')
                                    ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                    ->select('admin_returned_products.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image',
                                    'products.voltage as voltage', 'products.dimensions as dimensions', 'products.weight as weight', 'products.powerSources as powerSources', 
                                    'admin_returned_products.reason', 'admin_returned_products.created_at as requested_date_return')
                                    ->get();      
                                    
                                    
                                    $data->transform(function ($item) {
                                        $item->requested_date_return = $item->requested_date_return ? \Carbon\Carbon::parse($item->requested_date_return)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
                                        return $item;
                                    });

        return response()->json(['data' => $data]);
    }

    public function show()
    {
        $data = TrackOrder::leftJoin('products', 'products.id', '=', 'track_orders.product_id')
                        ->leftJoin('users', 'users.id', '=', 'products.user_id')
                        ->leftJoin('users as userAdmin', 'userAdmin.id', '=', 'track_orders.user_id')
                        ->select(
                            'track_orders.*', 
                            'products.brand as brand_name', 
                            'products.tool as tool_name', 
                            'products.powerSources as powerSources',
                            'products.voltage as voltage', 
                            'products.weight as weight', 
                            'products.dimensions as dimensions', 
                            'products.material as material',
                            'products.image as image', 
                            'users.name as supplier_name',
                            'userAdmin.location as location',
                            DB::raw('JSON_LENGTH(track_orders.serial_numbers) as stocks')
                        )
                        ->where('track_orders.is_completed', true)
                        ->where('track_orders.is_approved', false)
                        ->where('track_orders.serial_numbers', '!=', '[]')
                        ->where('track_orders.user_id', Auth::id())
                        ->get();
    
        // Decode the JSON string into an array
        foreach ($data as $order) {
            $order->serial_numbers = json_decode($order->serial_numbers);
        }

        return response()->json(['data' => $data]);
    }

    public function returnProduct(Request $request)
    {   
        $trackOrder = TrackOrder::find($request->dataValues['id']);
        $trackOrder->is_returned = true;
        $trackOrder->save();

        // Decode the JSON string of serial numbers stored in the database column
        $serialNumbers = json_decode($trackOrder->serial_numbers);

        // Remove the returned serial numbers from the array
        $returnedSerialNumbers = $request->serial_numbers;
        $serialNumbers = is_array($serialNumbers) ? array_diff($serialNumbers, $returnedSerialNumbers) : [];

        // Update the serial_numbers column in the database with the updated array
        $trackOrder->serial_numbers = json_encode($serialNumbers);
        $trackOrder->save();

        // Save each returned serial number along with the reason
        foreach ($returnedSerialNumbers as $serial_number) {
            $adminReturn = new AdminReturnedProducts();
            $adminReturn->track_order_id = $request->dataValues['id'];
            $adminReturn->serial_number = $serial_number;
            $adminReturn->reason = $request->reason;
            $adminReturn->save();

            $serialNumber = SerialNumber::where('serial_number', $serial_number)->first();
            if ($serialNumber) {
                $serialNumber->is_returned = true;
                $serialNumber->save();
            }
        }

        $history = new History();
        $history->user_id = Auth::id();
        $history->product_id = $request->dataValues['product_id'];
        $history->action = 'You Returned a Product';
        $history->save();

        return response()->json(['status' => 'success', 'message' => 'Product requested for return.']);
    }

    public function approvedProduct(Request $request)
    {   
        $trackOrder = TrackOrder::find($request->id);
        $trackOrder->is_approved = true;
        $trackOrder->save();
        
        // Check if a product with the same ID already exists in ToolsAndEquipment
        $existingProduct = ToolsAndEquipment::where('product_id', $trackOrder->product_id)->first();
    
        if ($existingProduct) {
            // Convert the existing serial numbers string to an array
            $existingSerialNumbers = json_decode($existingProduct->serial_numbers, true);
    
            // Merge new serial numbers with existing ones, ensuring no duplicates
            $mergedSerialNumbers = array_unique(array_merge($existingSerialNumbers, $request->serial_numbers));
            // Update the serial numbers field
            $existingProduct->serial_numbers = json_encode($mergedSerialNumbers);
            $existingProduct->stocks += count($request->serial_numbers);
            $existingProduct->save();
        } else {
            // Create a new entry
            $supplierName = Product::leftjoin('users', 'users.id', 'products.user_id')
                                    ->find($trackOrder->product_id);
    
            $toolsAndEquipment = new ToolsAndEquipment();
            $toolsAndEquipment->product_id = $trackOrder->product_id;
            $toolsAndEquipment->serial_numbers = json_encode($request->serial_numbers);
            $toolsAndEquipment->category_id = 1;
            $toolsAndEquipment->price = 0;
            $toolsAndEquipment->supplier_name = $supplierName->name;
            $toolsAndEquipment->stocks = count($request->serial_numbers);
            $toolsAndEquipment->save();
        }
    
        $history = new History();
        $history->user_id = Auth::id();
        $history->product_id = $request->product_id;
        $history->action = 'You approved a Product';
        $history->save();
    
        return response()->json(['status' => 'success', 'message' => 'Product approved.']);
    }
    
    
    
}
