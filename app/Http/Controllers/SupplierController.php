<?php

namespace App\Http\Controllers;

use App\Models\AdminHistory;
use App\Models\History;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderedProducts;
use App\Models\Product;
use App\Models\ReleasedProduct;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use App\Models\TrackOrder;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{   

    public function index(){
        return view('admin.supplier');
    }

    public function show(){
        
        $suppliers = User::where('role', 2)->get();

        $userLocation = User::where('id', auth()->id())->pluck('location');
        $userBalance = User::where('id', auth()->id())->pluck('balance');

        $products = Product::leftjoin('users', 'users.id', '=', 'products.user_id')
                            ->where('is_for_sale', true)
                            ->select('products.*', 'users.name as supplier_name','users.company_description as company_description')
                            ->get();

        return response()->json(['suppliers' => $suppliers, 'products' => $products, 'userLocation' => $userLocation, 'userBalance' => $userBalance]);
    }

    public function getId(Request $request)
    {
        $serialNumbers = SerialNumber::where('product_id', $request->id)
                                    ->where('is_selected', false)
                                    ->get();

        return response()->json(['serialNumbers' => $serialNumbers]);
    }
    
    public function filterData(Request $request)
    {
        $supplier_id = $request->supplier_id;
        $brand = $request->brand;
        $tool = $request->tool;
        $specs = $request->specs;
    
        $query = Product::query();
    
        if (!empty($supplier_id)) {
            $query->where('user_id', $supplier_id);
        }
    
        if (!empty($brand)) {
            $query->where('brand', 'like', '%' . $brand . '%');
        }
    
        if (!empty($tool)) {
            $query->where('tool', 'like', '%' . $tool . '%');
        }
    
        if (!empty($specs)) {
            $query->where('powerSources', 'like', '%' . $specs . '%');
        }

        $query->leftJoin('users', 'products.user_id', '=', 'users.id')
          ->select('products.*', 'users.name as supplier_name')
          ->where('is_for_sale', true);

        $products = $query->get();
        
        return response()->json(['products' => $products]);
    }

    public function purchaseProduct(Request $request)
    {       
            // $selectedProducts = $request->all();
            $orderNumber = 'ORD-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
            $trackingNumber = 'TRK-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
            $historyNumber = 'HIS-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
            $transactionNumber = 'TRN-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);

            //TOTAL PRICE
            $reviewTotalWithoutCurrency = str_replace('₱', '', $request->reviewTotal);
            $reviewTotalPriceNumeric = floatval($reviewTotalWithoutCurrency);

            //TOTAL VAT
            $reviewVATWithoutCurrency = str_replace('₱', '', $request->reviewVatTotal);
            $reviewVATNumeric = floatval($reviewVATWithoutCurrency);

            //TOTAL GRAND TOTAL
            $reviewGrandTotalWithoutCurrency = str_replace('₱', '', $request->reviewGrandTotal);
            $reviewGrandTotalNumeric = floatval($reviewGrandTotalWithoutCurrency);
 
            $user = User::find(Auth::id());
            $user->balance -= $reviewGrandTotalNumeric;
    
            if ($user->balance < 0) {
                return response()->json(['error' => 'Insufficient funds'], 400);
            }
    
            $user->save();

            foreach ($request->selectedProducts as $selectedProduct) {
                $selectedSerialNumbers = $selectedProduct['selectedSerialNumbers'];
                $serializedSerialNumbers = json_encode($selectedSerialNumbers);
                $productId = $selectedProduct['dataValues']['id'];

                //SUPPLIER RELEASED PRODUCT WILL BE SET AS SOLD
                $releasedProduct = ReleasedProduct::where('product_id', $productId)->first();
                $releasedProduct->is_sold = true;
                $releasedProduct->save();
    
                //track the order
                $trackOrder = new TrackOrder();
                $trackOrder->status = 'Pending';
                $trackOrder->product_id = $productId;
                $trackOrder->serial_numbers = $serializedSerialNumbers;
                $trackOrder->tracking_number = $trackingNumber;
                $trackOrder->total_price = $reviewGrandTotalNumeric;
                $trackOrder->user_id = Auth::id();
                $trackOrder->save();
    
                $product = Product::find($productId);
                $product->stocks -= count($selectedSerialNumbers);
                $product->save();
    
                foreach ($selectedSerialNumbers as $selectedSerialNumber) {
                    $serialNumber = SerialNumber::where('serial_number', $selectedSerialNumber)->first();
                    $serialNumber->is_selected = true;
                    $serialNumber->save();
                }

                $orderedProduct = new OrderedProducts();
                $orderedProduct->user_id = Auth::id();
                $orderedProduct->track_orders_id = $trackOrder->id; 
                $orderedProduct->shipment_date = '00/00/0000';
                $orderedProduct->delivery_date = '00/00/0000';
                $orderedProduct->order_number = $orderNumber;
                $orderedProduct->save();

                //NOTIFY THE SUPPLIER for how many orders
                $notification = new Notification();
                $notification->description = 'You have a new order';
                $notification->track_order_id = $trackOrder->id;
                $notification->type = 'Order';
                $notification->save();
            }

            //TRANSACTION HISTORY 
            $transactions = new Transactions();
            $transactions->user_id = Auth::id();
            $transactions->track_order_id = $trackOrder->id;
            $transactions->transaction_id = $transactionNumber;
            $transactions->transaction_type = 'Ordered';
            $transactions->description = 'A total of ' . '₱' . $reviewGrandTotalNumeric . ' has been deducted from your balance';
            $transactions->save();

            //HISTORY
            $history = new History();
            $history->user_id = Auth::id();
            $history->product_id = $productId;
            $history->history_number = $historyNumber;
            $history->action = 'You Purchased a Product ' . 'the total is' . ' ' . '₱' . $reviewTotalPriceNumeric .' ' . 'the Vat is ' . '₱' . $reviewVATNumeric . ' ' . 'the grand total price is ' . '₱' . $reviewGrandTotalNumeric . ' including VAT';
            $history->save();
            
            return response()->json(['message' => 'Products Purchased Successfully'], 200);
    
        // } catch (\Exception $e) {
        //     dd($e->getMessage());
        //     return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
        // }
    }
    
}
