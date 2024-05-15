<?php

namespace App\Http\Controllers;

use App\Models\AdminReleasedProducts;
use App\Models\AllProducts;
use App\Models\ToolsAndEquipment;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\History;
use App\Models\Maintenance;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PowerToolsController extends Controller
{
    public function index(){
        return view('admin.powertools');
    }
    
    public function show(){

        $data = ToolsAndEquipment::leftjoin('products', 'products.id', 'tools_and_equipment.product_id')
                                    ->leftjoin('categories', 'categories.id', 'tools_and_equipment.category_id')
                                    ->select('tools_and_equipment.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as image', 'categories.name as category_name'
                                    ,'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 
                                    'products.material as material', 'tools_and_equipment.id as tools_and_equipment_id',
                                    DB::raw('JSON_LENGTH(tools_and_equipment.serial_numbers) as stocks'))
                                    ->get();

        $categories = Category::get();

        $suppliers = User::where('role', 2)->get();

        foreach ($data as $order) {
            $order->serial_numbers = json_decode($order->serial_numbers);
        }

        return response()->json([ 'data' => $data, 'categories' => $categories, 'suppliers' => $suppliers ]);
    }

    public function filterData(Request $request){

        // dd($request->all());
        $supplier_name = $request->supplier_name;
        $category_number = $request->category_number;
        $brand = $request->brand;
        $tool = $request->tool;
        $specs = $request->specs;
        
        $query = ToolsAndEquipment::query();
    
        $query->leftJoin('products', 'products.id', '=', 'tools_and_equipment.product_id')
            ->leftJoin('users', 'users.id', '=', 'products.user_id')
            ->leftjoin('categories', 'tools_and_equipment.category_id', '=', 'categories.id')
            ->select('tools_and_equipment.*', 'categories.name as category_name', 'products.brand as brand_name', 'products.tool as tool_name', 
            'products.image as image', 'products.powerSources as product_powerSources', 'products.voltage as product_voltage',
            'products.weight as product_weight', 'products.dimensions as product_dimensions', 'products.material as product_material', 'users.name as supplier_name',
            DB::raw('JSON_LENGTH(tools_and_equipment.serial_numbers) as stocks'));
            
        // if (!empty($category_number)) {
        //     $query->where('tools_and_equipment.category_id', $category_number);
        // }

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

        if (!empty($category_number)) {
            $query->where('tools_and_equipment.category_id', 'like', '%' . $category_number . '%');
        }
        

        // $query->where('tools_and_equipment.is_delivered', true);
    
        $data = $query->get();

        $suppliers = User::where('role', 2)->get();

        // foreach ($data as $order) {
        //     $order->serial_numbers = json_decode($order->serial_numbers);
        // }

        return response()->json(['data' => $data, 'suppliers' => $suppliers]);
    
    }    

    public function releasedProduct(Request $request){
        $historyNumber = 'HIS-' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        
        $selectedProducts = $request->all();
        
        foreach ($selectedProducts as $selectedProduct) {
            foreach ($selectedProduct['selectedSerialNumbers'] as $serialNumber) {
                // Find the corresponding record in AllProducts by serial number
                $allProduct = AllProducts::where('serial_numbers', $serialNumber)->first();
                
                // If the record exists, update its status to 'Released'
                if ($allProduct) {
                    $allProduct->status = $selectedProduct['dataValues']['status'];;
                    $allProduct->save();
                }
            }
            // Check if the product_id already exists in AdminReleasedProducts
            $existingReleasedProduct = AdminReleasedProducts::where('tools_and_equipment_id', $selectedProduct['dataValues']['tools_and_equipment_id'])
            ->first();
            // If the product already exists, update the existing record
            if($existingReleasedProduct){
                // Merge the existing serial numbers with the new ones
                $existingSerialNumbers = json_decode($existingReleasedProduct->serial_numbers);
                $newSerialNumbers = $selectedProduct['selectedSerialNumbers'];
                if($existingReleasedProduct->status === $selectedProduct['dataValues']['status'])
                {
                    $mergedSerialNumbers = array_unique(array_merge($existingSerialNumbers, $newSerialNumbers));
                    $existingReleasedProduct->serial_numbers = json_encode($mergedSerialNumbers);
                    $existingReleasedProduct->status = $selectedProduct['dataValues']['status'];
                    $existingReleasedProduct->price = $selectedProduct['dataValues']['price'];
                    $existingReleasedProduct->save();
                }
                else{
                    $adminReleasedProducts = new AdminReleasedProducts();
                    $adminReleasedProducts->tools_and_equipment_id = $selectedProduct['dataValues']['id'];
                    $adminReleasedProducts->serial_numbers = json_encode($selectedProduct['selectedSerialNumbers']);
                    $adminReleasedProducts->status = $selectedProduct['dataValues']['status'];
                    $adminReleasedProducts->price = $selectedProduct['dataValues']['price'];
                    $adminReleasedProducts->save();
                }
                // $mergedSerialNumbers = array_unique(array_merge($existingSerialNumbers, $newSerialNumbers));
                // // Update the serial numbers and price of the existing record
                // $existingReleasedProduct->serial_numbers = json_encode($mergedSerialNumbers);
                // $existingReleasedProduct->status = $selectedProduct['dataValues']['status'];
                // $existingReleasedProduct->price = $selectedProduct['dataValues']['price'];
                // $existingReleasedProduct->save();
            } 
            else {
                // If the product doesn't exist, create a new record
                $adminReleasedProducts = new AdminReleasedProducts();
                $adminReleasedProducts->tools_and_equipment_id = $selectedProduct['dataValues']['tools_and_equipment_id'];
                $adminReleasedProducts->serial_numbers = json_encode($selectedProduct['selectedSerialNumbers']);
                $adminReleasedProducts->status = $selectedProduct['dataValues']['status'];
                $adminReleasedProducts->price = $selectedProduct['dataValues']['price'];

                $adminReleasedProducts->save();
            }

            // Update the serial numbers of the associated ToolsAndEquipment record
            $toolsAndEquipment = ToolsAndEquipment::find($selectedProduct['dataValues']['id']);
            
            if($toolsAndEquipment){
                // Remove the returned serial numbers from the array
                $serialNumbers = json_decode($toolsAndEquipment->serial_numbers);
                $returnedSerialNumbers = $selectedProduct['selectedSerialNumbers'];
                $serialNumbers = is_array($serialNumbers) ? array_diff($serialNumbers, $returnedSerialNumbers) : [];
                // Update the serial_numbers column in the database with the updated array
                $toolsAndEquipment->serial_numbers = json_encode($serialNumbers);
                $toolsAndEquipment->save();
            }

            // Log the history of the action
            $history = new History();
            $history->user_id = Auth::id();
            $history->product_id = $selectedProduct['dataValues']['product_id'];
            $history->history_number = $historyNumber;
            $history->action = 'You Released this Product for ' .  $selectedProduct['dataValues']['status'] . ' with a price of ' . '₱' . $selectedProduct['dataValues']['price'];
            $history->save();
        }
    
        return response()->json(['message' => 'Product has been released!']);
    }
    
    public function forMaintenance(Request $request)
    {   
        foreach ($request->serialNumbers as $serialNumber) {    
            // Create a new maintenance record
            $maintenance = new Maintenance();
            $maintenance->product_id = $request->dataValues['product_id'];
            $maintenance->serial_number = $serialNumber;
            $maintenance->status = 'For Maintenance';
            $maintenance->save();
    
            // Find and delete the corresponding ToolsAndEquipment record(s)
            $toolsAndEquipment = ToolsAndEquipment::whereJsonContains('serial_numbers', $serialNumber)->get();
            foreach ($toolsAndEquipment as $equipment) {
                $serialNumbers = json_decode($equipment->serial_numbers);
                // Remove the serial number from the array
                $updatedSerialNumbers = array_values(array_diff($serialNumbers, [$serialNumber]));
                // Update the serial_numbers column in the database with the updated array
                $equipment->serial_numbers = json_encode($updatedSerialNumbers);
                $equipment->save();
            }
        }
    }
    
    

}
