<?php

namespace App\Http\Controllers;

use App\Models\CanceledOrder;
use App\Models\CompletedOrderAdmin;
use App\Models\History;
use App\Models\OrderedProducts;
use App\Models\Receipts;
use App\Models\TrackOrder;
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
                            ->select('track_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.voltage as voltage', 'products.image as image',
                                    'products.dimensions as dimensions', 'products.weight as weight', 'products.powerSources as powerSources',
                                    'track_orders.created_at as completed_at', 'users.name as user_name', 'users.location as location')
                            ->where('track_orders.is_completed', true)
                           ->get(); 
                           
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
                            ->select('canceled_orders.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.voltage as voltage', 'products.image as image',
                                    'products.dimensions as dimensions', 'products.weight as weight', 'products.powerSources as powerSources',
                                    'canceled_orders.created_at as canceled_at', 'users.name as user_name', 'users.location as location',
                                    'track_orders.serial_numbers as serial_numbers', 'track_orders.total_price as total_price')
                            ->get();

        $data->transform(function ($item) {
        $item->canceled_at = $item->canceled_at ? \Carbon\Carbon::parse($item->canceled_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
        return $item;
        });

        return response()->json(['data' => $data]);
    }

    public function updateStatus(Request $request)
    {   
        $orderedProducts = OrderedProducts::find($request->id);
        
        // Parse and format the shipment date
        $shipmentDate = Carbon::parse($request->shipment_date)->format('m/d/Y');
        $orderedProducts->shipment_date = $shipmentDate;
        
        // Parse and format the delivery date
        $deliveryDate = Carbon::parse($request->delivery_date)->format('m/d/Y');
        $orderedProducts->delivery_date = $deliveryDate;
        $orderedProducts->save();

        $trackOrders = TrackOrder::find($orderedProducts->track_orders_id);
        $trackOrders->status = $request->status_data;
        $trackOrders->save();

        if($request->status_data == 'Completed'){
            $trackOrders->is_completed = true;
            $trackOrders->save();

            $completedOrder = new CompletedOrderAdmin();
            $completedOrder->track_order_id = $trackOrders->id;
            $completedOrder->save();

            $receipts = new Receipts();
            $receipts->track_order_id = $request->track_orders_id;
            $receipts->user_id = $request->user_id;
            $receipts->ordered_product_id = $request->id;
            $receipts->total_price = $request->total_price;
            $receipts->receipt_number = 'REC-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
            $receipts->save();

            $userBalance = User::find(Auth::id());
            $userBalance->balance += $request->total_price;
            $userBalance->save();
        }

        return response()->json(['message' => 'Status updated successfully']);
    }
}
