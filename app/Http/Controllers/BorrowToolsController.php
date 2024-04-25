<?php

namespace App\Http\Controllers;

use App\Models\BorrowTools;
use Illuminate\Http\Request;
use App\Models\DeliverHistory;
use Carbon\Carbon;
use App\Models\Borrowed;
use App\Models\Order;
use App\Models\ReturnDays;
use App\Models\Sold;
use App\Models\ToolsAndEquipment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BorrowToolsController extends Controller
{
    public function index()
    {
        return view('users.borrowtools');
    }

    public function history() {
        return view('users.borrowedhistory');
    }

    public function showHistory() {

        $user_id = Auth::id();
        $data = BorrowTools::where('user_id', $user_id)
            ->leftJoin('scaffoldings', 'scaffoldings.id', '=', 'borrow_tools.scaffoldings_id')
            ->leftjoin('users', 'users.id', '=', 'borrow_tools.user_id')
            ->leftJoin('return_days','return_days.id', '=', 'borrow_tools.return_days_id')
            ->select('borrow_tools.*', 'scaffoldings.name as scaffoldings_name', 'users.name as users_name', 'return_days.number_of_days as number_of_days')
            ->get();

        return response()->json([ 'data' => $data]);
    }

    public function show(){

        $data = ToolsAndEquipment::leftjoin('products', 'products.id', '=', 'tools_and_equipment.product_id')
        ->select('tools_and_equipment.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as product_image',
         'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 'products.material as material')
        ->where('category_id', 3)
        ->get();

        $returndays = ReturnDays::get();

        return response()->json([ 'data' => $data, 'returndays' => $returndays]);
    }

    public function filterData(Request $request)
    {
        $brand = $request->brand;
        $tool = $request->tool;
        $specs = $request->specs;
    
        $query = ToolsAndEquipment::query();

        $query->leftJoin('products', 'products.id', '=', 'tools_and_equipment.product_id')
        ->select('tools_and_equipment.*', 'products.brand as brand_name', 'products.tool as tool_name', 'products.image as product_image',
                 'products.powerSources as powerSources', 'products.voltage as voltage', 'products.weight as weight', 'products.dimensions as dimensions', 'products.material as material')
                 ->where('category_id', 3);
    
        if (!empty($brand)) {
            $query->where('products.brand', 'like', '%' . $brand . '%');
        }
    
        if (!empty($tool)) {
            $query->where('products.tool', 'like', '%' . $tool . '%');
        }
    
        if (!empty($specs)) {
            $query->where('products.powerSources', 'like', '%' . $specs . '%');
        }

        $data = $query->get();
        
        return response()->json(['data' => $data]);
    }

    public function store(Request $request) {
        $request->validate([
            'return_days_id' => 'required|integer',
        ], [
            'return_days_id.required' => 'The Number of Days field is required',
        ]);

        $user_id = Auth::id();

        // Find the user record
        $user = User::findOrFail($user_id);
        $remaining_balance = $user->balance - $request->price;

        if ($remaining_balance >= 0) {
            $user->balance = $remaining_balance;
            $user->save();
            
            $tools = ToolsAndEquipment::findOrFail($request->id);
            $tools->status = 'Borrowed';
            $tools->save();
            
            $borrowed = new Borrowed();
            $borrowed->user_id = $user->id;
            $borrowed->tools_and_equipment_id = $request->id;
            $borrowed->return_days_id = $request->return_days_id;
            $borrowed->status = 'Preparing';
        
            // Retrieve return days from database
            $returnDays = ReturnDays::findOrFail($request->return_days_id);
            // Calculate return date based on return days
            $returnDate = Carbon::now()->addDays($returnDays->number_of_days);
            $borrowed->return_date = $returnDate->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
            $borrowed->save();
        
            $delivery = new DeliverHistory();
            $delivery->user_id = $user->id;
            $delivery->tools_and_equipment_id = $request->id;
            $delivery->status = 'Preparing';
            $delivery->type = 'Borrow';
            $delivery->save();

            $orders = new Order();
            $orders->user_id =  $user->id;
            $orders->tools_and_equipment_id = $request->id;
            $orders->status = 'Preparing';
            $orders->type = 'Borrowing';
            $orders->save();

            $sold = new Sold();
            $sold->user_id = $user_id;
            $sold->tools_and_equipment_id = $request->id;
            $sold->type = 'Borrowing';
            $sold->sold_at = now();
            $sold->save();

            return response()->json(['message' => 'Data Successfully Saved']);
        } else {
            // Return response indicating insufficient funds
            return response()->json(['error' => 'Insufficient funds'], 400);
        }
    }
}
