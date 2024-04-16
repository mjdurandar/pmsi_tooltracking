<?php

namespace App\Http\Controllers;

use App\Models\AdminHistory;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\ToolsAndEquipment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index(){
        return view('admin.supplier');
    }

    public function show(){

        $suppliers = User::where('role', 2)->get();

        $products = Product::leftjoin('users', 'users.id', '=', 'products.user_id')
                            ->select('products.*', 'users.name as supplier_name')
                            ->get();

        return response()->json(['suppliers' => $suppliers, 'products' => $products]);
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
          ->select('products.*', 'users.name as supplier_name');

        $products = $query->get();
        
        return response()->json(['products' => $products]);
    }

    public function purchaseProduct(Request $request) {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);

        $requestedItems = $request->requestedItems;
        $productId = $request->id;
        $total = $request->total; 

        // // Deduct the total from the user's balance
        // $user->balance -= $total;
        // $user->save();

        $product = Product::findOrFail($productId);

        for ($i = 0; $i < $requestedItems; $i++) {
            $productCode = 'P-' . str_pad(ToolsAndEquipment::count() + 1, 3, '0', STR_PAD_LEFT);
            $tool = new ToolsAndEquipment();
            $tool->product_code = $productCode;
            $tool->product_id = $productId;
            $tool->price = 0;
            $tool->stocks =  1; 
            $tool->is_approved = 0;
            $tool->save();
        }

        $history = new AdminHistory();
        $history->tools_and_equipment_id = $tool->id;
        $history->items = $requestedItems;
        $history->total_price = $total;
        $history->user_id = $user->id;
        $history->status = 'Unrealeased';
        
        $history->save();

        $product->decrement('stocks', $requestedItems);

        return response()->json(['message' => 'Products Requested Successfully'], 200);
    }
    

}
