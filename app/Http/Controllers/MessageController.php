<?php

namespace App\Http\Controllers;

use App\Models\BrokenProduct;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        return view('admin.messages');
    }

    public function show()
    {
        $data = BrokenProduct::leftjoin('users', 'users.id', 'broken_products.user_id')
            ->select('broken_products.*', 'users.name as user_name', 'users.email as user_email', 'users.contact_address as user_phone', 'users.location as user_address')
            ->get();
            
        return response()->json(['data' => $data]);
    }
}
