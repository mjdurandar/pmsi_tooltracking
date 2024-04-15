<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index() {
        return view('supplier.product');
    }

    public function show() {
        $user_id = Auth::id();

        $data = Product::where('user_id', $user_id)->get();

        return response()->json([ 'data' => $data]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'brand' => 'required|string',
            'tool' => 'required|string',
            'image' => 'required|string',
            'powerSources' => 'required|string',
            'voltage' => 'required|string',
            'weight' => 'required|string',
            'dimensions' => 'required|string',
            'material' => 'required|string',
            'stocks' => 'required|string',
            'price' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'other_brand' => 'nullable|string|max:255',
            'other_tool' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        $data = isset($request->id) ? Product::where('id', $request->id)->first() : new Product();
        $data->user_id = $user->id;
        $data->brand = $request->brand;
        $data->tool = $request->tool;
        $data->image = $request->image;
        $data->powerSources = $request->powerSources;
        $data->voltage = $request->voltage;
        $data->weight = $request->weight;
        $data->dimensions = $request->dimensions;
        $data->material = $request->material;
        $data->stocks = $request->stocks;
        $data->price = $request->price;

        if ($request->brand === 'Other') {
            $data->brand = $request->other_brand;
        }
    
        if ($request->tool === 'Other') {
            $data->tool = $request->other_tool;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $data->image = $imageName;
        }

        $data->save();
        return response()->json(['message' => 'Product added successfully'], 200);
    }

    public function edit(Product $product)
    {
        return response()->json(['data' => $product]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['data' => $product]);
    }
}
