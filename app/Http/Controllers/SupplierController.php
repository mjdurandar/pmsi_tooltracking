<?php

namespace App\Http\Controllers;

use App\Models\AdminHistory;
use App\Models\History;
use App\Models\Order;
use App\Models\OrderedProducts;
use App\Models\Product;
use App\Models\ReleasedProduct;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\ToolsAndEquipment;
use App\Models\TrackOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{   

    public function index(){
        return view('admin.supplier');
    }

    public function show(){
        
        $suppliers = User::where('role', 2)->get();

        $userLocation = User::where('id', auth()->id())->pluck('location');
        $userBalance = User::where('id', auth()->id())->pluck('balance');

        $products = Product::leftjoin('users', 'users.id', '=', 'products.user_id')
                            ->where('is_for_sale', true)
                            ->select('products.*', 'users.name as supplier_name')
                            ->get();

        return response()->json(['suppliers' => $suppliers, 'products' => $products, 'userLocation' => $userLocation, 'userBalance' => $userBalance]);
    }

    public function getId(Request $request)
    {
        $serialNumbers = SerialNumber::where('product_id', $request->id)
                                    ->where('is_selected', false)
                                    ->get();

        return response()->json(['serialNumbers' => $serialNumbers]);
    }
    
    public function filterData(Request $request)
    {
        $supplier_id = $request->supplier_id;
        $brand = $request->brand;
        $tool = $request->tool;
        $specs = $request->specs;
    
        $query = Product::query();
    
        if (!empty($supplier_id)) {
            $query->where('user_id', $supplier_id);
        }
    
        if (!empty($brand)) {
            $query->where('brand', 'like', '%' . $brand . '%');
        }
    
        if (!empty($tool)) {
            $query->where('tool', 'like', '%' . $tool . '%');
        }
    
        if (!empty($specs)) {
            $query->where('powerSources', 'like', '%' . $specs . '%');
        }

        $query->leftJoin('users', 'products.user_id', '=', 'users.id')
          ->select('products.*', 'users.name as supplier_name')
          ->where('is_for_sale', true);

        $products = $query->get();
        
        return response()->json(['products' => $products]);
    }

    public function purchaseProduct(Request $request)
    {
        $selectedProducts = $request->all();
        $orderNumber = 'ORD-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        $trackingNumber = 'TRK-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);

        foreach ($selectedProducts as $selectedProduct) {
            $selectedSerialNumbers = $selectedProduct['selectedSerialNumbers'];
            $serializedSerialNumbers = json_encode($selectedSerialNumbers);
            $productId = $selectedProduct['dataValues']['id'];
    
            $releasedProduct = ReleasedProduct::findOrFail($productId);
            $releasedProduct->is_sold = true;
            $releasedProduct->save();
    
            $trackOrder = new TrackOrder();
            $trackOrder->status = 'Pending';
            $trackOrder->product_id = $productId;
            $trackOrder->serial_numbers = $serializedSerialNumbers;
            $trackOrder->tracking_number = $trackingNumber;
            $trackOrder->total_price = $selectedProduct['dataValues']['price'];
            $trackOrder->user_id = Auth::id();
            $trackOrder->save();
    
            $product = Product::find($productId);
            $product->stocks -= count($selectedSerialNumbers);
            $product->save();
    
            $user = User::find(Auth::id());
            $user->balance -= $selectedProduct['dataValues']['price'];
            $user->save();
    
            $orderedProduct = new OrderedProducts();
            $orderedProduct->user_id = Auth::id();
            $orderedProduct->track_orders_id = $trackOrder->id;
            $orderedProduct->shipment_date = '00/00/0000';
            $orderedProduct->delivery_date = '00/00/0000';
            $orderedProduct->order_number = $orderNumber;
            $orderedProduct->save();
    
            foreach ($selectedSerialNumbers as $selectedSerialNumber) {
                $serialNumber = SerialNumber::where('serial_number', $selectedSerialNumber)->first();
                $serialNumber->is_selected = true;
                $serialNumber->save();
            }
    
            $history = new History();
            $history->user_id = Auth::id();
            $history->product_id = $productId;
            $history->action = 'You Purchased a Product';
            $history->save();
        }
        
        return response()->json(['message' => 'Products Purchased Successfully'], 200);
    }
    

    

    // public function purchaseProduct(Request $request) {
    //     $selectedProducts = $request->all();
    //     dd($selectedProducts);
    //     $selectedSerialNumbers = $request->selectedSerialNumbers;
    //     $serializedSerialNumbers = json_encode($selectedSerialNumbers);

    //     $releasedProduct = ReleasedProduct::findOrFail($request->id);
    //     $releasedProduct->is_sold = true;
    //     $releasedProduct->save();

    //     //THE ORDER WILL BE TRACKED
    //     $trackOrder = new TrackOrder();
    //     $trackOrder->status = 'Pending';
    //     $trackOrder->product_id = $request->id;
    //     $trackOrder->serial_numbers = $serializedSerialNumbers;
    //     $trackOrder->total_price = $request->total;
    //     $trackOrder->user_id = Auth::id();
    //     $trackOrder->save();

    //     // THE PRODUCTS STOCKS WILL BE DEDUCTED
    //     $product = Product::find($request->id);
    //     $product->stocks -= count($selectedSerialNumbers);
    //     $product->save();

    //     //THE USER BALANCE WILL BE DEDUCTED
    //     $user = User::find(Auth::id());
    //     $user->balance -= $request->total;
    //     $user->save();

    //     //THE ORDERED PRODUCTS WILL SAVE IN SUPPLIER SIDE
    //     $orderedProduct = new OrderedProducts();
    //     $orderedProduct->user_id = Auth::id();
    //     $orderedProduct->track_orders_id = $trackOrder->id;
    //     // $orderedProduct->is_completed = false;
    //     // $orderedProduct->is_canceled = false;
    //     $orderedProduct->shipment_date = '00/00/0000';
    //     $orderedProduct->delivery_date = '00/00/0000';
    //     $orderedProduct->save();

    //     //THE SERIAL NUMBERS WILL BE SELECTED
    //     foreach ($selectedSerialNumbers as $selectedSerialNumber) {
    //         $serialNumber = SerialNumber::where('serial_number', $selectedSerialNumber)->first();
    //         $serialNumber->is_selected = true;
    //         $serialNumber->save();
    //     }

    //     $history = new History();
    //     $history->user_id = Auth::id();
    //     $history->product_id = $request->id;
    //     $history->action = 'You Purchased a Product';
    //     $history->save();
        
    //     return response()->json(['message' => 'Product Purchased Successfully'], 200);
    // }
    

}
