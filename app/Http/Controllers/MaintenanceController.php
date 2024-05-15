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

    public function filterData(Request $request)
    {   
        $serialNumber = $request->serialNumbers; // Check if the value is coming or not (You can remove this line after checking the value
    // Check the value of serialNumber (You can remove this line after checking the value
        $query = Maintenance::leftjoin('products','maintenances.product_id','products.id')
        ->select('maintenances.*','products.brand as brand_name', 'products.tool as tool_name', 'products.image as image');

        if (!empty($serialNumber)) {
            $query->where('maintenances.serial_number', 'like', '%' . $serialNumber . '%');
        }        

        $data = $query->get();

        return response()->json(['data' => $data]);
    
    }
}
