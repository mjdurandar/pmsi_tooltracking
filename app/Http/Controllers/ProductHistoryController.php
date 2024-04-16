<?php

namespace App\Http\Controllers;

use App\Models\AdminHistory;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use PDO;

class ProductHistoryController extends Controller
{
    public function index(){
        return view('admin.product-history');
    }

    public function show(){

        $userName = Auth::user()->name;

        $data = AdminHistory::leftjoin('tools_and_equipment', 'admin_histories.tools_and_equipment_id', '=', 'tools_and_equipment.id')
        ->leftjoin('products', 'tools_and_equipment.product_id', '=', 'products.id')
        ->leftjoin('users', 'products.user_id', '=', 'users.id')
        ->select('admin_histories.*', 'products.brand as product_brand', 'products.tool as product_tool', 'users.name as supplier_name')
        ->orderBy('admin_histories.created_at', 'desc')
        ->get();

        $suppliers = User::where('role', 2)->get();

        $statuses = Category::get();

        return response()->json([ 'data' => $data, 'suppliers' => $suppliers , 'statuses' => $statuses, 'userName' => $userName ]);
    }

    public function viewHistory(Request $request){
        
        $brand = $request->brand;
        $tool = $request->tool;
        $supplier_id = $request->supplier_id;

        $query = AdminHistory::query();

        $query->leftJoin('tools_and_equipment', 'admin_histories.tools_and_equipment_id', '=', 'tools_and_equipment.id')
        ->leftjoin('products', 'tools_and_equipment.product_id', '=', 'products.id')
        ->leftjoin('users', 'products.user_id', '=', 'users.id')
        ->select('admin_histories.*', 'products.brand as product_brand', 'products.tool as product_tool', 'users.name as supplier_name');
        
            
        if (!empty($brand)) {
            $query->where('products.brand', $brand);
        }

        if (!empty($tool)) {
            $query->where('products.tool', $tool);
        }
    
        if (!empty($supplier_id)) {
            $query->where('products.user_id', $supplier_id);
        }

        $data = $query->get();

        return response()->json([ 'data' => $data ]);
    }

}
