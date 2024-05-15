<?php

namespace App\Http\Controllers;

use App\Models\Receipts;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReceiptsController extends Controller
{
    public function index()
    {
        return view('admin.receipts');
    }

    public function show()
    {   
        $data = Receipts::leftJoin('users', 'receipts.user_id', 'users.id')
                        ->leftJoin('ordered_products', 'ordered_products.id', 'receipts.ordered_product_id')
                        ->leftJoin('track_orders', 'track_orders.id', 'receipts.track_order_id')
                        ->leftJoin('products', 'products.id', 'track_orders.product_id')
                        ->leftjoin('users as supplier', 'products.user_id', 'supplier.id')
                        ->select(
                            'receipts.*',
                            'users.name as user_name',
                            'users.contact_address as contact_address',
                            'users.location as location',
                            'receipts.created_at as order_date',
                            'products.brand as brand_name',
                            'products.tool as tool_name',
                            'track_orders.serial_numbers as serial_numbers',
                            'supplier.id as supplier_id'
                        )
                        ->where('receipts.user_id', Auth::id())
                        ->get();

                        if (Auth::id() === 1) {
                            // Get unique supplier IDs from the collection
                  
                            $supplierIds = $data->pluck('supplier_id')->unique()->toArray(); // Convert the collection to an array
             
                            // Query the User model to retrieve admin location based on supplier IDs
                            $adminLocation = User::whereIn('id', $supplierIds)->first();
                        } else {
                            // For admin users, use their own location
                            $adminLocation = User::find(Auth::id())->location;
                        }
                        

                        // if (Auth::id() === 1) {   
                        //     // Check if data is correct
                        //     $supplierIds = $data->pluck('supplier_id')->unique(); // Get unique supplier IDs from the collection
                        //     $adminLocation = User::where('id', $supplierIds)->first();
                        // } else {
                        //     // For admin users, use their own location
                        //     $adminLocation = User::find(Auth::id())->first();
                        // }
                        
        $data->transform(function ($item) {
            $item->order_date = $item->order_date ? \Carbon\Carbon::parse($item->order_date)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            // Convert serial numbers string to array
            $item->serial_numbers = explode(',', $item->serial_numbers);
            return $item;
        });

        return response()->json(['data' => $data, 'adminLocation' => $adminLocation,]);
    }

    public function filterData(Request $request){

        $receiptNumber = $request->receiptNumber;
        
        $data = Receipts::
                        leftJoin('users', 'receipts.user_id', 'users.id')
                        ->leftJoin('ordered_products', 'ordered_products.id', 'receipts.ordered_product_id')
                        ->leftJoin('track_orders', 'track_orders.id', 'receipts.track_order_id')
                        ->leftJoin('products', 'products.id', 'track_orders.product_id')
                        ->select(
                            'receipts.*',
                            'users.name as user_name',
                            'users.contact_address as contact_address',
                            'users.location as location',
                            'receipts.created_at as order_date',
                            'products.brand as brand_name',
                            'products.tool as tool_name',
                            'track_orders.serial_numbers as serial_numbers',
                        )
                        ->where('receipts.receipt_number', 'like', '%' . $receiptNumber . '%')
                        ->where('receipts.user_id', Auth::id())
                        ->get(); 
        
        return response()->json(['data' => $data]);
    
    }
    

}
