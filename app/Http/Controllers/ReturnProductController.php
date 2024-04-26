<?php

namespace App\Http\Controllers;

use App\Models\Borrowed;
use App\Models\PurchasedItems;
use App\Models\ReturnProduct;
use App\Models\ToolsAndEquipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReturnProductController extends Controller
{
    public function index()
    {
        return view('users.productreturn');
    }

    public function show()
    {   
        $user = auth()->user()->id;

        $userAdmin = User::where('name', 'admin')
                        ->first();
        
        $product = Borrowed::leftjoin('tools_and_equipment', 'tools_and_equipment.id','borroweds.tools_and_equipment_id')
                        ->leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                        ->leftjoin('return_days', 'return_days.id', 'borroweds.return_days_id')
                        ->where('borroweds.user_id', $user)
                        ->where('borroweds.status', 'Completed')
                        ->select('borroweds.*', 'tools_and_equipment.product_code as product_code', 'products.brand as brand_name', 'products.tool as tool_name', 'return_days.penalty as penalty')
                        ->get();

        return response()->json([ 'product' => $product, 'userAdmin' => $userAdmin ]);
    }

    public function store(Request $request)
    {   
        $request->validate([
            'delivery_date' => 'required',
        ], [
            'delivery_date.required' => "The Delivery Date field is required",
        ]);

        $user = auth()->user()->id;

        $toolsAndEquipmentId = ToolsAndEquipment::findorfail($request->tools_and_equipment_id);
        $toolsAndEquipmentId->status = 'Returning';
        $toolsAndEquipmentId->save();

        $borrowed = Borrowed::where('tools_and_equipment_id', $request->tools_and_equipment_id)->first();
        $borrowed->detail = 'Returning';
        $borrowed->save();

        if(Carbon::now() > new Carbon($borrowed->return_date))
        {
            $userBalance = User::where('id', $user)->first();
            $userBalance->balance -= $request->penalty;
            $userBalance->save();
        }

        $purchased = PurchasedItems::where('tools_and_equipment_id', $request->tools_and_equipment_id)->first();
        $purchased->notes = 'Returned';
        $purchased->save();

        $returnProduct = new ReturnProduct();
        $returnProduct->tools_and_equipment_id = $request->tools_and_equipment_id;
        $returnProduct->user_id = auth()->user()->id;
        $returnProduct->detail = 'Returning';
        $returnProduct->return_date = $request->delivery_date;
        $returnProduct->save();

        return response()->json([ 'message' => 'Product has been returned successfully!' ]);
    }
}
