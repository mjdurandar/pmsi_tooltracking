<?php

namespace App\Http\Controllers;

use App\Models\BrokenProduct;
use App\Models\CompletedOrderUser;
use App\Models\OrderedProducts;
use App\Models\TrackOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\select;

class UserProductController extends Controller
{
    public function index()
    {
        return view('users.users-product');
    }

    public function show()
    {
        $data = OrderedProducts::leftjoin('track_orders', 'track_orders.id', 'ordered_products.track_orders_id')
                                ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                ->select('ordered_products.*', 'products.brand as brand_name', 'products.tool as tool_name' ,'products.image as image')
                                ->where('is_user', true)
                                ->get();

            foreach ($data as $order) {
                $order->serial_numbers = json_decode($order->serial_numbers);
            }

        return response()->json(['data' => $data]);
    }

    public function brokenProduct(Request $request)
    {   
        $brokenProducts = new BrokenProduct();
        $brokenProducts->serial_number = $request->serial_number;
        $brokenProducts->user_id = Auth::id();
        $brokenProducts->description = $request->description;
        $brokenProducts->save();

        return response()->json(['message' => 'Broken Product Added Successfully']);
    }
}
