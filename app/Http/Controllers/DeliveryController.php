<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;

class DeliveryController extends Controller
{
    public function index() {
        return view('users.delivery');
    }

    public function show() {
        $data = Delivery::leftjoin('power_tools', 'power_tools.id', '=', 'deliveries.power_tools_id')
                            ->leftjoin('users', 'users.id', '=', 'deliveries.user_id')
                            ->select('deliveries.*','power_tools.name as power_tools_name', 'users.name as user_name')
                            ->get();
        return response()->json([ 'data' => $data ]);
    }

}
