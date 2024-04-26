<?php

namespace App\Http\Controllers;

use App\Models\Borrowed;
use App\Models\ReturnProduct;
use App\Models\ToolsAndEquipment;
use Illuminate\Http\Request;

class ReturnedProductController extends Controller
{
    public function index()
    {
        return view('admin.returned-product');
    }

    public function show() 
    {
        $user = auth()->user()->id;

        $data = ReturnProduct::leftjoin('tools_and_equipment', 'tools_and_equipment.id', 'return_products.tools_and_equipment_id')
                                ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                                ->leftjoin('users', 'users.id', 'return_products.user_id')
                                ->select('return_products.*', 'products.brand as brand_name', 'products.tool as tool_name','tools_and_equipment.product_code as product_code', 
                                'users.name as user_name', 'users.email as user_email', 'users.contact_address as user_phone')
                                ->get();

        // $data = ToolsAndEquipment::leftjoin('products', 'products.id','tools_and_equipment.product_id')
        //                         ->where('status', 'Returning')
        //                         ->select('tools_and_equipment.*', 'products.brand as brand_name', 'products.tool as tool_name')
        //                         ->get();

        return response()->json([ 'data' => $data]);
    }

    public function damaged(Request $request)
    {
        $data = ReturnProduct::find($request->id);
        $data->damage_description = $request->damage_description;
        $data->save();

        if($data->damage_description){
            $borrowed = Borrowed::where('tools_and_equipment_id', $request->tools_and_equipment_id)->first();
            $borrowed->detail = 'Damaged';
            $borrowed->save();
        }

        return response()->json(['message' => 'Data Successfully Saved']);
    }

    public function store(Request $request)
    {   
        $toolsAndEquipmentId = ToolsAndEquipment::where('id', $request->tools_and_equipment_id)->first();
        $toolsAndEquipmentId->status = 'For Sale';
        $toolsAndEquipmentId->save();

        $borrowed = Borrowed::where('tools_and_equipment_id', $request->tools_and_equipment_id)->first();
        $borrowed->detail = 'Completed';
        $borrowed->save();

        return response()->json(['message' => 'Product Released']);

    }
}
