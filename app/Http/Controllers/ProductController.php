<?php

namespace App\Http\Controllers;

use App\Models\CanceledProduct;
use App\Models\History;
use App\Models\Product;
use App\Models\ProductCode;
use App\Models\ReleasedProduct;
use App\Models\SerialNumber;
use App\Models\TrackOrder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{   
    private $imageUrl="";

    public function index() {
        return view('supplier.product');
    }

    public function show() {
        $user_id = Auth::id();

        $data = Product::where('user_id', $user_id)
                        ->where('is_removed', false)
                        ->where('is_canceled', false)
                        ->get();

        return response()->json([ 'data' => $data]);
    }

    public function store(Request $request)
    {   
        $serialNumbers = json_decode($request->serial_numbers);
        $historyNumber = 'HIS-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        
        // dd($request->serial_numbers);
        if(count($serialNumbers) !== count(array_unique($serialNumbers)))
        {   
            return response()->json(['error' => 'Duplicate Codes are not allowed.'], 422);
        }
        else{
            $user = auth()->user();

            $data = isset($request->id) ? Product::where('id', $request->id)->first() : new Product();

            $action = isset($request->id) ? 'Edited a Product' : 'Added a Product';
    
            $data->user_id = $user->id;
            $data->brand = $request->brand;
            $data->tool = $request->tool;
    
            if($this->imageUrl) {
                $data->image = $this->imageUrl;
            }
            else{
                $data->image = $request->image;
            }
    
            $data->powerSources = $request->powerSources;
            $data->voltage = $request->voltage;
            $data->weight = $request->weight;
            $data->dimensions = $request->dimensions;
            $data->material = $request->material;
            $data->stocks = $request->stocks;
            $data->price = $request->price;
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $data->image = $imageName;
            }

            // If restocked quantity is provided, update the stocks attribute
            if ($request->reStocked) {
                $data->stocks += intval($request->reStocked);
            }

            foreach ($serialNumbers as $serialNumber) {
                // Check if the serial number already exists in the database
                $existingSerial = SerialNumber::where('serial_number', $serialNumber)->exists();
            
                // If the serial number already exists, return an error
                if ($existingSerial) {
                    return response()->json(['error' => 'Serial number already exists!.'], 422);
                }
            
                // Find the existing serial number associated with the product
                $existingSerial = SerialNumber::where('product_id', $data->id)
                                            ->where('serial_number', $serialNumber)
                                            ->first();
            
                // If the existing serial number is found, update it
                if ($existingSerial) {
                    // Update the existing serial number
                    $existingSerial->update([
                        'product_id' => $data->id,
                        'serial_number' => $serialNumber,
                    ]);
                } else {
                    // If not found, create a new serial number entry
                    $data->save();
                    $serial = new SerialNumber();
                    $serial->product_id = $data->id;
                    $serial->serial_number = $serialNumber;
                    $serial->save();
                }
            }            

            $history = new History();
            $history->user_id = $user->id;
            $history->product_id = $data->id;
            $history->history_number = $historyNumber;
            $history->action = $action;
            $history->save();
            
            return response()->json(['message' => 'Product added successfully'], 200);
        }
    }    

    public function edit(Product $product)
    {   
        $imageUrl = $product->image;

        $this->imageUrl = $imageUrl;

        $product = Product::findOrFail($product->id);
        $serialNumbers = $product->serialNumbers()->pluck('serial_number')->toArray();

        
        return response()->json([
            'data' => $product,
            'serial_numbers' => $serialNumbers,
        ]);
    }
    
    public function isRemoved(Request $request)
    {   
        $historyNumber = 'HIS-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        $product = Product::find($request->id);
        $product->is_removed = true;
        $product->save();

        $history = new History();
        $history->user_id = Auth::id();
        $history->product_id = $request->id;
        $history->history_number = $historyNumber;
        $history->action = 'You Removed a Product';
        $history->save();

        $product->serialNumbers()->delete();
        
        return response()->json(['data' => $product]);
    }

    public function releasedProduct(Request $request)
    {   
        $historyNumber = 'HIS-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);

        $releasedProduct = new ReleasedProduct();
        $releasedProduct->product_id = $request->id;
        $releasedProduct->is_supplier = true;
        $releasedProduct->save();

        $product = Product::find($request->id);
        $product->is_for_sale = true;
        $product->save();

        $history = new History();
        $history->user_id = Auth::id();
        $history->product_id = $request->id;
        $history->history_number = $historyNumber;
        $history->action = 'Released a Product';
        $history->save();

        return response()->json(['message' => 'Product released successfully'], 200);
    }

    public function canceledProduct(Request $request)
    {     
        $trackOrder = TrackOrder::where('product_id', $request->id)->first();
        $historyNumber = 'HIS-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        if($trackOrder){
            return response()->json(['errors' => 'Product is already ordered.'], 422);
        }
        else{
            $cancelledProduct = new CanceledProduct();
            $cancelledProduct->product_id = $request->id;
            $cancelledProduct->description = $request->dataValues['description'];
            $cancelledProduct->save();
    
            $product = Product::find($request->id);
            $product->is_canceled = true;
            $product->is_for_sale = false;
            $product->save();
    
            $history = new History();
            $history->user_id = Auth::id();
            $history->product_id = $request->id;
            $history->history_number = $historyNumber;
            $history->action = 'Canceled a Product';
            $history->save();
    
            return response()->json(['message' => 'Product canceled successfully'], 200);
        }

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
    
        // Check if status is set to 1 (true), then filter for products that are for sale
        if ($status == 1) {
            $query->where('products.is_for_sale', true);
        }
        // Check if status is set to 0 (false), then filter for products that are not for sale
        elseif ($status == 0) {
            $query->where('products.is_for_sale', false);
        }
        
        $query->where('products.is_canceled', false);
        $data = $query->get();
    
        return response()->json(['data' => $data]);
    
    }    
}
