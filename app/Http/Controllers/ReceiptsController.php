<?php

namespace App\Http\Controllers;

use App\Models\Receipts;
use Illuminate\Support\Facades\Auth;

class ReceiptsController extends Controller
{
    public function index()
    {
        return view('admin.receipts');
    }

    public function show()
    {
        $data = Receipts::leftJoin('users', 'receipts.user_id', 'users.id')
                        ->leftJoin('ordered_products', 'ordered_products.id', 'receipts.ordered_product_id')
                        ->leftJoin('track_orders', 'track_orders.id', 'receipts.track_order_id')
                        ->leftJoin('products', 'products.id', 'track_orders.product_id')
                        ->select(
                            'receipts.*',
                            'users.name as user_name',
                            'users.contact_address as contact_address',
                            'users.location as location',
                            'receipts.created_at as order_date',
                            'products.brand as brand_name',
                            'products.tool as tool_name',
                            'track_orders.serial_numbers as serial_numbers',
                        )
                        ->where('receipts.user_id', Auth::id())
                        ->get();

        $data->transform(function ($item) {
            $item->order_date = $item->order_date ? \Carbon\Carbon::parse($item->order_date)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : null;
            // Convert serial numbers string to array
            $item->serial_numbers = explode(',', $item->serial_numbers);
            return $item;
        });

        return response()->json(['data' => $data]);
    }

}
