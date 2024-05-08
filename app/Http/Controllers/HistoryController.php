<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        return view('admin.history');
    }

    public function show()
    {
        $data = History::leftJoin('users', 'histories.user_id', '=', 'users.id')
                        ->leftjoin('products', 'products.id', '=', 'histories.product_id')
                        ->select('histories.*', 'users.name as user_name', 'histories.action as action_name', 
                        'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image', 
                        'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight' ,'products.dimensions as dimensions', 'products.material as material')
                        ->where('histories.user_id', Auth::user()->id)
                        ->get();
        
        return response()->json(['data' => $data]);
    }
}
