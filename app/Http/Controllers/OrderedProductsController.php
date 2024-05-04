<?php

namespace App\Http\Controllers;

use App\Models\ToolsAndEquipment;
use Illuminate\Http\Request;

class OrderedProductsController extends Controller
{
    public function index()
    {
        return view('supplier.ordered-product');
    }

    public function show()
    {
        $data = ToolsAndEquipment::leftJoin('products', 'products.id', 'tools_and_equipment.product_id')
                                ->selectRaw('MIN(tools_and_equipment.id) as id, 
                                            products.brand as brand_name, 
                                            products.tool as tool_name, 
                                            products.created_at as date_ordered, 
                                            products.image as image,
                                            products.voltage as voltage, 
                                            products.dimensions as dimensions, 
                                            products.weight as weight, 
                                            products.powerSources as powerSources,
                                            tools_and_equipment.is_delivered') // Include is_delivered column
                                ->groupBy('tools_and_equipment.product_id', 
                                        'products.brand', 
                                        'products.tool', 
                                        'products.created_at', 
                                        'products.image', 
                                        'products.voltage', 
                                        'products.dimensions', 
                                        'products.weight', 
                                        'products.powerSources', 
                                        'tools_and_equipment.is_delivered') // Group by is_delivered as well
                                ->where('tools_and_equipment.is_delivered', 0) // Filter only those that are not delivered
                                ->get();

        return response()->json(['data' => $data]);
    }
}
