<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        return view('admin.maintenance');
    }

    public function show()
    {
        $data = Maintenance::leftjoin('products','maintenances.product_id','products.id')
            ->select('maintenances.*','products.brand as brand_name', 'products.tool as tool_name', 'products.image as image')
            ->get();

        return response()->json(['data' => $data]);
    }
}
