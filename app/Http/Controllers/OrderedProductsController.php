<?php

namespace App\Http\Controllers;

use App\Models\OrderedProducts;
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
        $data = OrderedProducts::leftJoin('tools_and_equipment', 'tools_and_equipment.id', 'ordered_products.tools_and_equipment_id')
        ->leftJoin('products', 'products.id', 'tools_and_equipment.product_id')
        ->leftjoin('track_orders', 'track_orders.id', 'ordered_products.track_orders_id')
        ->selectRaw('MAX(ordered_products.id) as id, MAX(ordered_products.tools_and_equipment_id) as tools_and_equipment_id, 
                    MAX(ordered_products.created_at) as created_at, MAX(ordered_products.updated_at) as updated_at, 
                    MAX(ordered_products.status) as status,
                    MAX(products.brand) as brand_name, MAX(products.tool) as tool_name, MAX(products.image) as image, 
                    MAX(products.voltage) as voltage, MAX(products.dimensions) as dimensions, MAX(products.weight) as weight, 
                    MAX(products.powerSources) as powerSources, MAX(tools_and_equipment.is_delivered) as is_delivered,
                    MAX(track_orders.created_at) as ordered_at')
        ->groupBy('products.created_at')
        ->get();
    

        // $data = ToolsAndEquipment::leftJoin('products', 'products.id', 'tools_and_equipment.product_id')
        //                         ->selectRaw('MIN(tools_and_equipment.id) as id, 
        //                                     products.brand as brand_name, 
        //                                     products.tool as tool_name, 
        //                                     products.created_at as date_ordered, 
        //                                     products.image as image,
        //                                     products.voltage as voltage, 
        //                                     products.dimensions as dimensions, 
        //                                     products.weight as weight, 
        //                                     products.powerSources as powerSources,
        //                                     tools_and_equipment.is_delivered') // Include is_delivered column
        //                         ->groupBy('tools_and_equipment.product_id', 
        //                                 'products.brand', 
        //                                 'products.tool', 
        //                                 'products.created_at', 
        //                                 'products.image', 
        //                                 'products.voltage', 
        //                                 'products.dimensions', 
        //                                 'products.weight', 
        //                                 'products.powerSources', 
        //                                 'tools_and_equipment.is_delivered') // Group by is_delivered as well
        //                         ->where('tools_and_equipment.is_delivered', 0) // Filter only those that are not delivered
        //                         ->get();

        return response()->json(['data' => $data]);
    }
}
