<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        return view('admin.category');
    }
    
    public function show(){
        $data = Category::get();
        return response()->json([ 'data' => $data, ]);
    }

    public function store(Request $request)
    {       
        
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => "The Name field is required",
        ]);

        $data = isset($request->id) ? Category::where('id', $request->id)->first() : new Category();
        $data->name = $request->name;
        $data->save();
        return response()->json(['message' => 'Data Successfully Saved']);
    }

    public function edit(Category $category)
    {
        return response()->json(['data' => $category]);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['data' => $category]);
    }
}
