<?php

namespace App\Http\Controllers;

use App\Models\Borrowed;
use Illuminate\Http\Request;

class BorrowHistoryController extends Controller
{
    public function index() {
        return view('users.borrowedhistory');
    }

    public function show() {

        $data = Borrowed::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'borroweds.tools_and_equipment_id')
                ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                ->leftjoin('return_days', 'return_days.id', 'borroweds.return_days_id')
                ->select('borroweds.*', 'products.brand as brand_name', 'products.tool as tool_name', 'tools_and_equipment.product_code as product_code'
                ,'return_days.number_of_days as number_of_days', 'borroweds.created_at as created_at', 'tools_and_equipment.price as price')
                ->get();
        return response()->json([ 'data' => $data, ]);
    }

    public function filterData(Request $request)
    {
        $brand = $request->brand;
        $tool = $request->tool;
    
        $query = Borrowed::query();

        $query->leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'borroweds.tools_and_equipment_id')
                ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                ->leftjoin('return_days', 'return_days.id', 'borroweds.return_days_id')
                ->select('tools_and_equipment.*', 'products.brand as brand_name', 'products.tool as tool_name', 'return_days.number_of_days as number_of_days')
                 ->where('category_id', 3);
    
        if (!empty($brand)) {
            $query->where('products.brand', 'like', '%' . $brand . '%');
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }

        $data = $query->get();
        
        return response()->json(['data' => $data]);
    }
}
