<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QRController extends Controller
{     
    public function index($id) {

        $data = Order::where('id', $id)
                        ->get();

        return view('users.qr', ['data' => $data]);

    }

}
