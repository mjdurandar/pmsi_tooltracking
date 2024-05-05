<?php

namespace App\Http\Controllers;

use App\Models\AdminHistory;
use App\Models\OrderedProducts;
use App\Models\Product;
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

    public function purchaseProduct(Request $request) {
        $uniqueId = mt_rand(100000, 999999);

        $selectedSerialNumbers = $request->selectedSerialNumbers;
        $serialNumbers = SerialNumber::whereIn('serial_number', $selectedSerialNumbers)->get();
        
        foreach ($serialNumbers as $serialNumber) {
            $serialNumber->is_selected = true;
            $serialNumber->save();

            $product = Product::find($serialNumber->product_id);
            $product->stocks -= 1;
            $product->save();

            $user = User::find($product->user_id);
      
            $toolsAndEquipment = new ToolsAndEquipment();
            $toolsAndEquipment->product_id = $request->id;
            $toolsAndEquipment->stocks = 1;
            $toolsAndEquipment->category_id = 1;
            $toolsAndEquipment->price = 0;
            $toolsAndEquipment->supplier_name = $user->name;
            $toolsAndEquipment->is_approved = 0;
            $toolsAndEquipment->serial_number_id = $serialNumber->id;
            $toolsAndEquipment->is_delivered = 0;
            $toolsAndEquipment->save();

            $trackOrder = new TrackOrder();
            $trackOrder->tools_and_equipment_id = $toolsAndEquipment->id;
            $trackOrder->status = 'Pending';
            $trackOrder->unique_id = $uniqueId;
            $trackOrder->user_id = Auth::id();
            $trackOrder->save();

            $orderedProduct = new OrderedProducts();
            $orderedProduct->tools_and_equipment_id = $request->id;
            $orderedProduct->user_id = Auth::id();
            $orderedProduct->status = 'Pending';
            $orderedProduct->track_orders_id = $trackOrder->id;
            $orderedProduct->is_completed = false;
            $orderedProduct->is_canceled = false;
            $orderedProduct->save();
        }

        $user = User::find(Auth::id());
        $user->balance -= $request->total;
        $user->save();
        

        // $user_id = Auth::id();
        // $user = User::findOrFail($user_id);
        
        // $product = Product::findOrFail($request->id);
        // $product->stocks -= $request->requestedItems;

        // $serialNumbers = SerialNumber::where('product_id',$request->id)->get();

        // if($product->stocks <= 0)
        // {
        //     return response()->json(['error' => 'Low Stocks'], 400);
        // }

        // $requestedItems = $request->requestedItems;
        // $productId = $request->id;
        // $total = $request->total; 

        // $remaining_balance = $user->balance - $total;

        // if ($remaining_balance >= 0) {
        //     $product = Product::findOrFail($productId);
        //     $user->balance = $remaining_balance;
        //     $user->save();
        //     for ($i = 0; $i < $requestedItems; $i++) {
        //         $productCode = 'P-' . str_pad(ToolsAndEquipment::count() + 1, 3, '0', STR_PAD_LEFT);
        //         $tool = new ToolsAndEquipment();
        //         $tool->product_code = $productCode;
        //         $tool->product_id = $productId;
        //         $tool->price = 0;
        //         $tool->stocks =  1; 
        //         $tool->is_approved = 0;
        //         $tool->save();
        //     }
    
        //     $history = new AdminHistory();
        //     $history->tools_and_equipment_id = $tool->id;
        //     $history->items = $requestedItems;
        //     $history->total_price = $total;
        //     $history->user_id = $user->id;
        //     $history->status = 'Unrealeased';
            
        //     $history->save();
    
        //     $product->decrement('stocks', $requestedItems);

        //     return response()->json(['message' => 'Products Requested Successfully'], 200);

        // } else {
        //     // Return response indicating insufficient funds
        //     return response()->json(['error' => 'Insufficient funds'], 400);
        // }
    
    }
    

}
