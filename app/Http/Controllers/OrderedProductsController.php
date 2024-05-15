<?php

namespace App\Http\Controllers;

use App\Models\CanceledOrder;
use App\Models\CompletedOrderAdmin;
use App\Models\History;
use App\Models\Notification;
use App\Models\OrderedProducts;
use App\Models\Receipts;
use App\Models\TrackOrder;
use App\Models\Transactions;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\New_;

use function Laravel\Prompts\select;

class OrderedProductsController extends Controller
{
    public function index()
    {
        return view('supplier.ordered-product');
    }

    public function completedIndex()
    {
        return view('supplier.completed-ordered-product');
    }

    public function canceledIndex()
    {
        return view('supplier.canceled-ordered-product');
    }

    public function show()
    {   
        $authUser = auth()->user();
        $userName = User::findOrFail($authUser->id);
        $supplierName = $userName->name;

        $data = OrderedProducts::leftJoin('track_orders', 'ordered_products.track_orders_id', 'track_orders.id')
        ->leftJoin('products', 'track_orders.product_id', 'products.id')
        ->leftjoin('users', 'track_orders.user_id', 'users.id')
        ->leftjoin('users as admin', 'products.user_id', 'admin.id')
        ->leftjoin('users as supplier', 'supplier.id', 'products.user_id')
        ->select(
            'ordered_products.*', 
            'products.brand as brand_name', 
            'products.tool as tool_name',
            'track_orders.status as status', 
            'track_orders.created_at as ordered_at', 
            'users.name as user_name',
            'users.location as location', 
            'products.image as image', 
            'products.dimensions as dimensions', 
            'products.weight as weight', 
            'products.powerSources as powerSources',
            'track_orders.total_price as total_price',
            'users.location as location',
            'admin.name as supplier_name',
            'users.contact_address as contact_address',
            'users.email as email',
            'track_orders.serial_numbers as serial_numbers',
            DB::raw('LENGTH(track_orders.serial_numbers) - LENGTH(REPLACE(track_orders.serial_numbers, ",", "")) + 1 as serial_numbers_count')
        )
        ->whereNotIn('track_orders.status', ['Canceled', 'Completed'])
        ->where('admin.name', $supplierName)
        ->where('ordered_products.is_admin', false)
        // ->whereNull('admin.id') 
        ->get();

        foreach ($data as $order) {
            $order->serial_numbers = json_decode($order->serial_numbers);
        }

        $data->transform(function ($item) {
            $item->ordered_at = $item->ordered_at ? \Carbon\Carbon::parse($item->ordered_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });

        return response()->json(['data' => $data]);
    }
    
    public function completedShow()
    {
        $data = TrackOrder::leftjoin('products', 'track_orders.product_id', 'products.id')
                            ->leftjoin('users', 'track_orders.user_id', 'users.id')
                            ->leftjoin('users as supplier', 'supplier.id', 'products.user_id')
                            ->select('track_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.voltage as voltage', 'products.image as image',
                                    'products.dimensions as dimensions', 'products.weight as weight', 'products.powerSources as powerSources',
                                    'track_orders.created_at as completed_at', 'users.name as user_name', 'users.location as location')
                            ->where('track_orders.is_completed', true)
                            ->where('supplier.id', Auth::id())
                            ->where('is_admin', true)
                            ->get(); 

                            foreach ($data as $order) {
                                $order->serial_numbers = json_decode($order->serial_numbers);
                            }
                           
        $data->transform(function ($item) {
            $item->completed_at = $item->completed_at ? \Carbon\Carbon::parse($item->completed_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });

        return response()->json(['data' => $data]);
    }

    public function canceledShow()
    {
        $data = CanceledOrder::leftjoin('track_orders', 'canceled_orders.track_order_id', 'track_orders.id')
                            ->leftjoin('products', 'track_orders.product_id', 'products.id')
                            ->leftjoin('users', 'track_orders.user_id', 'users.id')
                            ->leftjoin('users as supplier', 'supplier.id', 'products.user_id')
                            ->select('canceled_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.voltage as voltage', 'products.image as image',
                                    'products.dimensions as dimensions', 'products.weight as weight', 'products.powerSources as powerSources',
                                    'canceled_orders.created_at as canceled_at', 'users.name as user_name', 'users.location as location',
                                    'track_orders.serial_numbers as serial_numbers', 'track_orders.total_price as total_price')
                            ->where('supplier.id', Auth::id())
                            ->get();

        $data->transform(function ($item) {
        $item->canceled_at = $item->canceled_at ? \Carbon\Carbon::parse($item->canceled_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
        return $item;
        });

        foreach ($data as $order) {
            $order->serial_numbers = json_decode($order->serial_numbers);
        }

        return response()->json(['data' => $data]);
    }

    public function updateStatus(Request $request)
    {   
        $orderedProducts = OrderedProducts::find($request->id);
        $transactionNumber = 'TRN-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        // Parse and format the shipment date
        $shipmentDate = Carbon::parse($request->shipment_date)->format('m/d/Y');
        $orderedProducts->shipment_date = $shipmentDate;
        
        // Parse and format the delivery date
        $deliveryDate = Carbon::parse($request->delivery_date)->format('m/d/Y');
        $orderedProducts->delivery_date = $deliveryDate;
        $orderedProducts->save();

        $trackOrders = TrackOrder::find($orderedProducts->track_orders_id);
        $trackOrders->status = $request->status_data;
        $trackOrders->is_admin = true;
        $trackOrders->save();

        $orderedProducts->status = $request->status_data;
        $orderedProducts->save();

        //product now of the admin
        $orderedProducts->is_admin = true;
        $orderedProducts->save();

        $trackOrders->is_completed = true;
        $trackOrders->save();

        $completedOrder = new CompletedOrderAdmin();
        $completedOrder->track_order_id = $trackOrders->id;
        $completedOrder->save();

        $notification = Notification::where('track_order_id', $request->track_orders_id)->first();
        $notification->is_done = true;
        $notification->save();

        if($request->status_data == 'Completed'){
            // Check if all orders with the same order number are completed
            $allOrdersCompleted = OrderedProducts::where('order_number', $orderedProducts->order_number)
            ->where('status', '<>', 'Completed')
            ->doesntExist();

            if ($allOrdersCompleted) {
                 // Aggregate product details to create a single receipt entry
                 $receiptEntries = [];
                  // Retrieve all ordered products with the same order number
                 $allOrderedProducts = OrderedProducts::leftjoin('track_orders', 'ordered_products.track_orders_id', 'track_orders.id')
                                        ->leftjoin('products', 'track_orders.product_id', 'products.id')
                                        ->where('order_number', $orderedProducts->order_number)->get();
                   
                 foreach ($allOrderedProducts as $product) {
                     $receiptEntries[] = [
                         'brand_name' => $product->brand,
                         'tool_name' => $product->tool,
                         'price' => $trackOrders->total_price,
                         'quantity' => $request->serial_numbers_count,
                         'serial_numbers' => $product->serial_numbers,
                     ];

                 }

                $receipts = new Receipts();
                $receipts->track_order_id = $request->track_orders_id;
                $receipts->user_id = $request->user_id;
                $receipts->ordered_product_id = $request->id;
                $receipts->total_price = $request->total_price;
                $receipts->entries = json_encode($receiptEntries);
                $receipts->receipt_number = 'REC-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
                $receipts->save();

                $userBalance = User::find(Auth::id());
                $userBalance->balance += $request->total_price;
                $userBalance->save();

                //TRANSACTION HISTORY 
                $transactions = new Transactions();
                $transactions->user_id = Auth::id();
                $transactions->track_order_id = $trackOrders->id;
                $transactions->transaction_id = $transactionNumber;
                $transactions->transaction_type = 'Ordered';
                $transactions->description = 'A total of ' . '₱' . $request->total_price . ' has been credited to your account';
                $transactions->save();

            }
        }

        return response()->json(['message' => 'Status updated successfully']);
    }
    
    public function completedFilterData(Request $request)
    {
        $brand = $request->brand;
        $tool = $request->tool;
        $serialNumber = $request->serialNumber;
    
        $query = TrackOrder::leftjoin('products', 'track_orders.product_id', 'products.id')
        ->leftjoin('users', 'track_orders.user_id', 'users.id')
        ->leftjoin('users as supplier', 'supplier.id', 'products.user_id')
        ->select('track_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.voltage as voltage', 'products.image as image',
                'products.dimensions as dimensions', 'products.weight as weight', 'products.powerSources as powerSources',
                'track_orders.created_at as completed_at', 'users.name as user_name', 'users.location as location')
        ->where('track_orders.is_completed', true)
        ->where('supplier.id', Auth::id());

        if (!empty($brand)) {
            $query->where('products.brand', $brand);
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }

        if (!empty($serialNumber)) {
            $query->where('track_orders.serial_number', 'like', '%' . $serialNumber . '%');
        }
    
        $data = $query->get();

        foreach ($data as $order) {
            $order->serial_numbers = json_decode($order->serial_numbers);
        }
       
        $data->transform(function ($item) {
        $item->completed_at = $item->completed_at ? \Carbon\Carbon::parse($item->completed_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
        return $item;
        });

        return response()->json(['data' => $data]);
    }

    public function filterData(Request $request)
    {      
        $authUser = auth()->user();
        $userName = User::findOrFail($authUser->id);
        $supplierName = $userName->name;

        $brand = $request->brand;
        $tool = $request->tool;
        $orderNumber = $request->orderNumber;
    
        $query = OrderedProducts::leftJoin('track_orders', 'ordered_products.track_orders_id', 'track_orders.id')
        ->leftJoin('products', 'track_orders.product_id', 'products.id')
        ->leftjoin('users', 'track_orders.user_id', 'users.id')
        ->leftjoin('users as admin', 'products.user_id', 'admin.id')
        ->select(
            'ordered_products.*', 
            'products.brand as brand_name', 
            'products.tool as tool_name',
            'track_orders.status as status', 
            'track_orders.created_at as ordered_at', 
            'users.name as user_name',
            'users.location as location', 
            'products.image as image', 
            'products.dimensions as dimensions', 
            'products.weight as weight', 
            'products.powerSources as powerSources',
            'track_orders.total_price as total_price',
            'users.location as location',
            'admin.name as supplier_name',
            'users.contact_address as contact_address',
            'users.email as email',
            'track_orders.serial_numbers as serial_numbers',
            DB::raw('LENGTH(track_orders.serial_numbers) - LENGTH(REPLACE(track_orders.serial_numbers, ",", "")) + 1 as serial_numbers_count')
        )
        ->whereNotIn('track_orders.status', ['Canceled', 'Completed'])
        ->where('admin.name', $supplierName);
        
        if (!empty($brand)) {
            $query->where('products.brand', $brand);
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }

        if (!empty($orderNumber)) {
            $query->where('ordered_products.order_number', 'like', '%' . $orderNumber . '%');
        }
    
        $data = $query->get();

        foreach ($data as $order) {
            $order->serial_numbers = json_decode($order->serial_numbers);
        }

        $data->transform(function ($item) {
            $item->ordered_at = $item->ordered_at ? \Carbon\Carbon::parse($item->ordered_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });

        return response()->json(['data' => $data]);
    }
    
}
