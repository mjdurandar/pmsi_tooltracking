<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductHistoryController extends Controller
{
    public function index(){
        return view('admin.product-history');
    }
}
