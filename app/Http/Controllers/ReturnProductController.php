<?php

namespace App\Http\Controllers;

use App\Models\Borrowed;
use App\Models\BorrowedProduct;
use App\Models\History;
use App\Models\PurchasedItems;
use App\Models\ReturnProduct;
use App\Models\ToolsAndEquipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReturnProductController extends Controller
{
    public function index()
    {
        return view('users.productreturn');
    }

    public function show()
    {      
        $userAdmin = User::where('name', 'PMSI')->first();

        $data = BorrowedProduct::leftJoin('ordered_products', 'borrowed_products.ordered_product_id', 'ordered_products.id')
                                ->leftJoin('track_orders', 'track_orders.id', 'ordered_products.track_orders_id')
                                ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                ->select('borrowed_products.*','products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
                                ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
                                'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status', 'track_orders.total_price as total_price'
                                ,'track_orders.serial_numbers as serial_numbers')
                                ->where('borrowed_products.is_returned', false)
                                ->where('borrowed_products.is_delivered', true)
                                ->get();

                                

        return response()->json([ 'data' => $data, 'userAdmin' => $userAdmin]);
    }

    public function returningProductIndex()
    {
        return view('users.returning-product');
    }

    public function returningProductShow()
    {       
        $userAdmin = User::where('name', 'PMSI')->first();

        $data = BorrowedProduct::leftJoin('ordered_products', 'borrowed_products.ordered_product_id', 'ordered_products.id')
                                ->leftJoin('track_orders', 'track_orders.id', 'ordered_products.track_orders_id')
                                ->leftjoin('products', 'products.id', 'track_orders.product_id')
                                ->select('borrowed_products.*','products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
                                ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
                                'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status', 'products.image as image' ,'track_orders.total_price as total_price'
                                ,'track_orders.serial_numbers as serial_numbers')
                                ->where('borrowed_products.is_returned', true)
                                ->get();

        return response()->json([ 'data' => $data , 'userAdmin' => $userAdmin]);   
    }

    public function store(Request $request)
    {   
        $user = User::findOrFail(Auth::id());
        $user->balance -= $request->penalty;
        $user->save();

        if($user->balance < 0) {
            return response()->json(['message' => 'You have Insufficient Balance to Pay the Penalty! Please add Balance or the Admin will contact you for more details.'], 400);
        }

        $deliveryDate = $request->delivery_date;
        $formattedDate = date('m/d/Y', strtotime($deliveryDate));
        
        $borrowedProducts = BorrowedProduct::findOrFail($request->id);
        $borrowedProducts->is_returned = true;
        $borrowedProducts->arrival_date = $formattedDate;
        $borrowedProducts->save(); 

        $history = new History();
        $history->user_id = Auth::id();
        $history->product_id = $request->id;
        $history->action = 'You Requested to Return this Product after Borrowing!';
        $history->save();


        return response()->json([ 'message' => 'You Initiated a Product Return!' ]);
    }

    public function filterData(Request $request)
    {   
        $userAdmin = User::where('name', 'PMSI')->first();

        $brand = $request->brand;
        $tool = $request->tool;
    
        $query = BorrowedProduct::leftJoin('ordered_products', 'borrowed_products.ordered_product_id', 'ordered_products.id')
        ->leftJoin('track_orders', 'track_orders.id', 'ordered_products.track_orders_id')
        ->leftjoin('products', 'products.id', 'track_orders.product_id')
        ->select('borrowed_products.*','products.brand as brand_name', 'products.tool as tool_name', 'products.image as image'
        ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 
        'products.dimensions as dimensions', 'products.material as material', 'track_orders.status as status', 'track_orders.total_price as total_price'
        ,'track_orders.serial_numbers as serial_numbers')
        ->where('borrowed_products.is_returned', false)
        ->where('borrowed_products.is_delivered', true);
    
        if (!empty($brand)) {
            $query->where('products.brand', $brand);
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }
    
        $data = $query->get();

        $data->transform(function ($item) {
            $item->requested_date_return = $item->requested_date_return ? \Carbon\Carbon::parse($item->requested_date_return)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            return $item;
        });

        return response()->json(['data' => $data]);

    }
}
