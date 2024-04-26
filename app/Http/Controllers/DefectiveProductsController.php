<?php

namespace App\Http\Controllers;

use App\Models\DamagedReturn;
use App\Models\DefectiveProducts;
use App\Models\ToolsAndEquipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DefectiveProductsController extends Controller
{
    public function index() 
    {
        return view('admin.defective-products');
    }

    public function show() 
    {

        $data = DamagedReturn::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'damaged_returns.tools_and_equipment_id')
                                ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                                ->leftjoin('users', 'users.id', 'damaged_returns.user_id')
                                ->select('damaged_returns.*', 'products.brand as brand_name', 'products.tool as tool_name','tools_and_equipment.product_code as product_code', 
                                'users.name as user_name', 'users.email as user_email', 'users.contact_address as user_phone')
                                ->get();

        return response()->json([ 'data' => $data ]);
    }

    public function damaged(Request $request)
    {
        $data = DamagedReturn::find($request->id);
        $data->damaged_description = $request->damaged_description;
        $data->save();

        return response()->json(['message' => 'Data Successfully Saved']);
    }

    public function store(Request $request)
    {   
        $toolsAndEquipmentId = ToolsAndEquipment::where('id', $request->tools_and_equipment_id)->first();
        $toolsAndEquipmentId->status = 'Damaged';
        $toolsAndEquipmentId->save();

        $data = DamagedReturn::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'damaged_returns.tools_and_equipment_id')
                                ->leftjoin('users', 'users.id', 'damaged_returns.user_id')
                                ->where('tools_and_equipment_id', $request->tools_and_equipment_id)
                                ->select('damaged_returns.*', 'users.balance as balance', 'users.id as user_id')
                                ->first();
        
        $user = Auth::user();
        $user->balance -= $data->price;
        $user->save();

        $user = User::where('id', $data->user_id)->first();
        $user->balance -= $data->price;
        $user->save();

        return response()->json(['message' => 'Product Released']);

    }
}
