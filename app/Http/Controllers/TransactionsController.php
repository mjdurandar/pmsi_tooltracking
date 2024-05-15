<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionsController extends Controller
{
    public function index()
    {
        return view('admin.transactions');
    }

    public function show()
    {
        $data = Transactions::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        $data->transform(function ($item) {
            $item->created_at = $item->created_at ? \Carbon\Carbon::parse($item->created_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });
        
        return response()->json(['data' => $data]);
    }
}
