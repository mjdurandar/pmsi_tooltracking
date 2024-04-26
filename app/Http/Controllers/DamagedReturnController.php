<?php

namespace App\Http\Controllers;

use App\Models\DamagedReturn;
use App\Models\PurchasedItems;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DamagedReturnController extends Controller
{
    public function index()
    {
        return view('users.damaged-return');
    }

    public function show()
    {   
        $user_id = Auth::id();

        $userAdmin = User::where('name', 'admin')->first();

        $data = PurchasedItems::leftjoin('tools_and_equipment', 'tools_and_equipment.id', '=', 'purchased_items.tools_and_equipment_id')
                                ->leftjoin('damaged_returns', 'damaged_returns.id', '=', 'purchased_items.damaged_return_id')
                                ->leftjoin('products', 'products.id', '=', 'tools_and_equipment.product_id')
                                ->leftjoin('users', 'users.id', '=', 'purchased_items.user_id')
                                ->select('purchased_items.*', 'products.brand as brand_name', 'products.tool as tool_name', 'tools_and_equipment.product_code as product_code',
                                'damaged_returns.damaged_description as damaged_description', 'damaged_returns.delivery_date as delivery_date', 'damaged_returns.status as status_damage')
                                ->where('users.id', $user_id)
                                ->where('purchased_items.details', 'Completed')
                                ->where(function ($query) {
                                    $query->where('purchased_items.notes', '!=', 'Returned')
                                          ->orWhereNull('purchased_items.notes');
                                })
                                ->get();
                                

        return response()->json([ 'data' => $data, 'userAdmin' => $userAdmin ]);
    }

    public function store(Request $request)
    {   
        $request->validate([
            'damaged_description' => 'required',
            'delivery_date' => 'required',
        ], [
            'damaged_description.required' => "The Damaged Description field is required",
            'delivery_date.required' => "The Delivery Date field is required",
        ]);

        $data = new DamagedReturn();
        $data->user_id = Auth::id();
        $data->tools_and_equipment_id = $request->tools_and_equipment_id;
        $data->delivery_date = $request->delivery_date;
        $data->damaged_description = $request->damaged_description;
        $data->status = 'Damaged';
        $data->save(); 

        $purchase = PurchasedItems::where('tools_and_equipment_id', $request->tools_and_equipment_id)->first();
        $purchase->damaged_return_id = $data->id;
        $purchase->save();

        return response()->json(['message' => 'Product will be Returned']);

    }
}
