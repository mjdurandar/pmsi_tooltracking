<?php

namespace App\Http\Controllers;

use App\Models\ToolsAndEquipment;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\AdminHistory;
use App\Models\CancelHistory;
use App\Models\PowerTools;
use App\Models\Unit;
use App\Models\Supplier;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PowerToolsController extends Controller
{
    public function index(){
        return view('admin.powertools');
    }
    
    public function show(){
        $data = ToolsAndEquipment::leftjoin('categories', 'tools_and_equipment.category_id', '=', 'categories.id')
                                ->leftjoin('products', 'tools_and_equipment.product_id', '=', 'products.id')
                                ->leftjoin('users', 'users.id', '=', 'products.user_id')
                                ->select('tools_and_equipment.*', 'categories.name as category_name', 'products.brand as product_brand', 'products.tool as product_tool', 
                                'products.image as product_image', 'products.powerSources as product_powerSources', 'products.voltage as product_voltage',
                                'products.weight as product_weight', 'products.dimensions as product_dimensions', 'products.material as product_material', 'users.name as supplier_name')
                                ->get();

        $categories = Category::get();

        $suppliers = User::where('role', 2)->get();

        return response()->json([ 'data' => $data, 'categories' => $categories, 'suppliers' => $suppliers ]);
    }

    public function filterData(Request $request){

        $supplier_name = $request->supplier_name;
        $category_number = $request->category_number;
        $brand = $request->brand;
        $tool = $request->tool;
        $specs = $request->specs;
        
        $query = ToolsAndEquipment::query();
    
        $query->leftJoin('products', 'products.id', '=', 'tools_and_equipment.product_id')
            ->leftJoin('users', 'users.id', '=', 'products.user_id')
            ->leftjoin('categories', 'tools_and_equipment.category_id', '=', 'categories.id')
            ->select('tools_and_equipment.*', 'categories.name as category_name', 'products.brand as product_brand', 'products.tool as product_tool', 
            'products.image as product_image', 'products.powerSources as product_powerSources', 'products.voltage as product_voltage',
            'products.weight as product_weight', 'products.dimensions as product_dimensions', 'products.material as product_material', 'users.name as supplier_name');
            
        if (!empty($category_number)) {
            $query->where('tools_and_equipment.category_id', $category_number);
        }

        if (!empty($supplier_name)) {
            $query->where('products.user_id', $supplier_name);
        }
    
        if (!empty($brand)) {
            $query->where('products.brand', 'like', '%' . $brand . '%');
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }
    
        if (!empty($specs)) {
            $query->where('products.powerSources', 'like', '%' . $specs . '%');
        }
    
        $products = $query->get();
    
        return response()->json(['products' => $products]);
    
    }    

    public function releaseProduct(ToolsAndEquipment $toolsAndEquipment){
        
        $toolsAndEquipment = ToolsAndEquipment::leftjoin('categories', 'tools_and_equipment.category_id', '=', 'categories.id')
                                ->leftjoin('suppliers', 'tools_and_equipment.supplier_id', '=', 'suppliers.id')
                                ->select('tools_and_equipment.*', 'categories.name as category_name', 'suppliers.name as supplier_name')
                                ->where('tools_and_equipment.id', $toolsAndEquipment->id)
                                ->first();

        return response()->json(['data' => $toolsAndEquipment]);
    }

    public function updateProduct(Request $request){
        
        $toolsAndEquipment = ToolsAndEquipment::findOrFail($request->id);
        $selectedCategory = $request->selectedCategory;
        $selectedPrice = $request->selectedPrice; 

        // $categoryName = Category::findOrFail($selectedCategory)->name;
        
        // $adminHistory = new AdminHistory();
        // $adminHistory->tools_and_equipment_id = $toolsAndEquipment->id; 
        // $adminHistory->status = $categoryName;
        // $adminHistory->save();

        $toolsAndEquipment->category_id = $selectedCategory;
        $toolsAndEquipment->price = $selectedPrice;

        if($selectedCategory === 1){
            $toolsAndEquipment->is_approved = 0;
            $toolsAndEquipment->status = 'Unrealeased';
        }
        else if ($selectedCategory === 2){
            $toolsAndEquipment->is_approved = 1;
            $toolsAndEquipment->status = 'For Sale';
        }
        else if ($selectedCategory === 3){
            $toolsAndEquipment->is_approved = 1;
            $toolsAndEquipment->status = 'For Borrowing';
        }

        $toolsAndEquipment->save();
        
    }

    public function cancelProduct(Request $request){
        $userId = Auth::user()->id;

        $adminHistory = new CancelHistory();
        
        $selectedId = $request->id;
        $description = $request->description; 

        $toolsAndEquipment = ToolsAndEquipment::findOrFail($selectedId);
        $toolsAndEquipment->category_id = 1;
        $toolsAndEquipment->price = 0;
        $toolsAndEquipment->is_approved = 0;
        $toolsAndEquipment->status = 'Canceled';

        $adminHistory->tools_and_equipment_id = $selectedId;
        $adminHistory->user_id = $userId;
        $adminHistory->cancel_reason = $description;

        $toolsAndEquipment->save();
        $adminHistory->save();

    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string',
    //         'price' => 'required|numeric',
    //         'category_id' => 'required|exists:categories,id',
    //         'unit_id' => 'required|exists:units,id',
    //         'supplier_id' => 'required|exists:suppliers,id',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     $request->validate([
    //         'name' => 'required|string',
    //         'price' => 'required|numeric',
    //         'category_id' => 'required|exists:categories,id',
    //         'unit_id' => 'required|exists:units,id',
    //         'supplier_id' => 'required|exists:suppliers,id',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ], [
    //         'name.required' => "The Name field is required",
    //         'price.required' => "The Price field is required",
    //         'category_id.required' => "The Category field is required",
    //         'unit_id.required' => "The Unit field is required",
    //         'supplier_id.required' => "The Supplier field is required",
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     // Check if an ID is provided
    //     if (isset($request->id)) {
    //         // Attempt to find the existing record
    //         try {
    //             $powerTool = PowerTools::findOrFail($request->id);
    //         } catch (\Exception $e) {
    //             // Log the error for debugging
    //             \Log::error('Error finding PowerTool by ID: ' . $e->getMessage());

    //             // Return a response indicating that the record was not found
    //             return response()->json(['error' => 'Power Tool not found'], 404);
    //         }
    //     } else {
    //         // If no ID is provided, create a new instance
    //         $powerTool = new PowerTools();
    //     }

    //     $productCode = 'P-' . str_pad(PowerTools::count() + 1, 3, '0', STR_PAD_LEFT);

    //     $powerTool = isset($request->id) ? PowerTools::where('id', $request->id)->first() : new PowerTools();
    //     $powerTool->name = $request->name;
    //     $powerTool->price = $request->price;
    //     $powerTool->category_id = $request->category_id;
    //     $powerTool->unit_id = $request->unit_id;
    //     $powerTool->supplier_id = $request->supplier_id;
    //     $powerTool->product_code = $productCode;

    //     // Check if an image file is uploaded
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $imageName = time() . '.' . $image->getClientOriginalExtension();
    //         $image->move(public_path('images'), $imageName);
    //         $powerTool->image = $imageName;
    //     }

    //     $powerTool->save();

    //     return response()->json(['message' => 'Power Tool added successfully'], 200);
    // }

    // public function edit(PowerTools $powertools)
    // {
    //     return response()->json(['data' => $powertools]);
    // }

    // public function destroy(PowerTools $powertools)
    // {
    //     $powertools->delete();
    //     return response()->json(['data' => $powertools]);
    // }
}
