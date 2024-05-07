<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Delivery;
use App\Models\OrderedProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QRController extends Controller
{     
    public function index($id) {

        $data = OrderedProducts::leftjoin('track_orders', 'track_orders.id', 'ordered_products.track_orders_id')
                        ->where('track_orders.id', $id)
                        ->where('is_canceled', false)
                        ->select('ordered_products.*', 'track_orders.status as status')
                        ->get();

        return view('users.qr', ['data' => $data]);

    }

}
