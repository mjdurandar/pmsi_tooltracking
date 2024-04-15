<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Supplier;
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
    

}
