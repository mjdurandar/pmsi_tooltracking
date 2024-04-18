<?php

namespace App\Http\Controllers;

use App\Models\DeliverHistory;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QRController extends Controller
{
    public function index() {
        return view('users.qr');
    }

    public function show() {
        $id = Auth::id();
   
        $data = DeliverHistory::where('user_id', $id)->get();

        return response()->json([ 'data' => $data ]);
    }
}
