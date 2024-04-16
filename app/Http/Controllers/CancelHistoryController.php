<?php

namespace App\Http\Controllers;

use App\Models\CancelHistory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CancelHistoryController extends Controller
{
    public function index() {
        return view('admin.cancel-history');
    }

    public function show() {
        // $userName = Auth::user()->name;
        $data = CancelHistory::leftJoin('tools_and_equipment', 'tools_and_equipment.id', '=', 'cancel_histories.tools_and_equipment_id')
                            ->leftJoin('products', 'products.id', '=', 'tools_and_equipment.product_id')
                            ->leftJoin('users as product_user', 'product_user.id', '=', 'products.user_id')
                            ->leftJoin('users', 'users.id', '=', 'cancel_histories.user_id')
                            ->select('cancel_histories.*', 'products.brand as brand_name', 'products.tool as tool_name', 'product_user.name as supplier_name', 
                                    'users.name as user_name', 'tools_and_equipment.product_code as product_code')
                            ->get()
                            ->map(function ($item) {
                                $item->created_at = $item->created_at->format('Y-m-d H:i:s');
                                return $item;
                            });

                            $suppliers = User::where('role', 2)->get();

        return response()->json([ 'data' => $data , 'suppliers' => $suppliers ]);
    }

    public function viewHistory(Request $request){
        
        $brand = $request->brand;
        $tool = $request->tool;
        $supplier_id = $request->supplier_id;

        $query = CancelHistory::query();

        $query->leftJoin('tools_and_equipment', 'cancel_histories.tools_and_equipment_id', '=', 'tools_and_equipment.id')
        ->leftjoin('products', 'tools_and_equipment.product_id', '=', 'products.id')
        ->leftJoin('users as product_user', 'product_user.id', '=', 'products.user_id')
        ->leftJoin('users', 'users.id', '=', 'cancel_histories.user_id')
        ->select('cancel_histories.*', 'products.brand as brand_name', 'products.tool as tool_name', 'product_user.name as supplier_name', 'users.name as user_name', 'tools_and_equipment.product_code as product_code');
        
            
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
