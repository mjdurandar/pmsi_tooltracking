<?php

namespace App\Http\Controllers;

use App\Models\AdminHistory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Category;
use PDO;

class ProductHistoryController extends Controller
{
    public function index(){
        return view('admin.product-history');
    }

    public function show(){

        $data = AdminHistory::leftjoin('tools_and_equipment', 'admin_histories.tools_and_equipment_id', '=', 'tools_and_equipment.id')
        ->leftjoin('suppliers', 'tools_and_equipment.supplier_id', '=', 'suppliers.id')
        ->select('admin_histories.*', 'tools_and_equipment.name as tools_and_equipment_name', 'tools_and_equipment.product_code as product_code', 'tools_and_equipment.price as price', 'suppliers.name as supplier_name')
        ->orderBy('admin_histories.created_at', 'desc')
        ->get()
        ->map(function ($item) {
            $item->created_at = $item->created_at->format('Y-m-d H:i:s');
            return $item;
        });

        $suppliers = Supplier::get();
        $statuses = Category::get();

        return response()->json([ 'data' => $data, 'suppliers' => $suppliers , 'statuses' => $statuses]);
    }

    public function viewHistory(Request $request){
        
        $brand = $request->input('brand');
        $tool = $request->input('tool');
        $supplier_id = $request->input('supplier_id');
        $status_id = $request->input('status_id');

        $data = AdminHistory::leftjoin('tools_and_equipment', 'admin_histories.tools_and_equipment_id', '=', 'tools_and_equipment.id')
        ->leftjoin('suppliers', 'tools_and_equipment.supplier_id', '=', 'suppliers.id')
        ->select('admin_histories.*', 'tools_and_equipment.name as tools_and_equipment_name', 'tools_and_equipment.product_code as product_code', 'tools_and_equipment.price as price', 'suppliers.name as supplier_name')
        ->orderBy('admin_histories.created_at', 'desc')
        ->where('tools_and_equipment.name', 'like', '%' . $tool . '%')
        ->where('tools_and_equipment.name', 'like', '%' . $brand . '%')
        ->where('tools_and_equipment.supplier_id', $supplier_id)
        ->where('admin_histories.status', $status_id)
        ->get()
        ->map(function ($item) {
            $item->created_at = $item->created_at->format('Y-m-d H:i:s');
            return $item;
        });

        return response()->json([ 'data' => $data ]);
    }

}
