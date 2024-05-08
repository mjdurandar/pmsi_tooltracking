<?php

namespace App\Http\Controllers;

use App\Models\CanceledProduct;
use App\Models\History;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanceledProductController extends Controller
{
    public function index() 
    {
        return view('supplier.canceled-product');
    }

    public function show()
    {
        $data = CanceledProduct::leftjoin('products', 'canceled_products.product_id', 'products.id')
                                ->where('products.is_canceled', true)
                                ->get();
  
        return response()->json([ 'data' => $data ]);
    }

    public function release(Request $request)
    {   
        $product = Product::find($request->id);
        $product->is_canceled = false;
        $product->save();

        $cancelProduct = CanceledProduct::where('product_id', $product->id)->first();
        $cancelProduct->delete();

        $history = new History();
        $history->user_id = Auth::id();
        $history->product_id = $request->id;
        $history->action = 'After Cancelation you released the product again';
        $history->save();

        return response()->json([ 'message' => 'Product released successfully' ]);
    }

    public function filterData(Request $request){

        $brand = $request->brand;
        $tool = $request->tool;
        $status = $request->status;
        
        $query = Product::query();
    
        if (!empty($brand)) {
            $query->where('products.brand', 'like', '%' . $brand . '%');
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }
    
        if (!empty($status)) {
            $query->where('products.is_for_sale', 'like', '%' . $status . '%');
        }
    
        $products = $query->get();
    
        return response()->json(['data' => $products]);
    
    }    
}
