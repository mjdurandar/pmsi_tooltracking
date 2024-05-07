<?php

namespace App\Http\Controllers;

use App\Models\AdminReturnedProducts;
use App\Models\Product;
use App\Models\SerialNumber;
use App\Models\ToolsAndEquipment;
use App\Models\TrackOrder;
use Illuminate\Http\Request;
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
                                    ->select('track_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'admin_returned_products.reason')
                                    ->get();

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

        $adminReturn = new AdminReturnedProducts();
        $adminReturn->track_order_id = $request->id;
        $adminReturn->reason = $request->reason;
        $adminReturn->save();

        foreach ($request->serial_numbers as $serial_number) {
            $serialNumber = SerialNumber::where('serial_number', $serial_number)->first();
            $serialNumber->is_returned = true;
            $serialNumber->save();
        }

        return response()->json(['status' => 'success', 'message' => 'Product requested for return.']);
    }

    public function approvedProduct(Request $request)
    {
        $trackOrder = TrackOrder::find($request->id);
        $trackOrder->is_approved = true;
        $trackOrder->save();
        
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

        return response()->json(['status' => 'success', 'message' => 'Product approved.']);
    }
}
