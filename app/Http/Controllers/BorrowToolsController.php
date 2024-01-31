<?php

namespace App\Http\Controllers;

use App\Models\BorrowTools;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Scaffolding;
use App\Models\ReturnDays;
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

        $categories = Category::get();
        $units = Unit::get();
        $suppliers = Supplier::get();
        $returndays = ReturnDays::get();
        $data = Scaffolding::leftjoin('categories', 'categories.id', '=', 'scaffoldings.category_id')
                            ->leftjoin('units', 'units.id', '=', 'scaffoldings.unit_id')
                            ->leftjoin('suppliers', 'suppliers.id', '=', 'scaffoldings.supplier_id')
                            ->select('scaffoldings.*','categories.name as category_name', 'units.name as unit_name', 'suppliers.name as supplier_name')
                            ->get();
        return response()->json([ 'data' => $data, 'categories' => $categories, 'units' => $units, 'suppliers' => $suppliers, 'returndays' => $returndays ]);
    }

    public function store(Request $request) {
        $request->validate([
            'return_days_id' => 'required|integer',
        ],
        [
            'return_days_id.required' => "The Number of Days field is required",
        ]);
        
        $user_id = auth()->user()->id;
        // Update is_sold_out field for the purchased tool
        $tool = Scaffolding::findOrFail($request->scaffoldings_id);
        $tool->is_borrowed = true; // Assuming is_sold_out is a boolean field
        $tool->save();
        // Proceed with storing BuyTools data
        $data = new BorrowTools();
        $data->user_id = $user_id;
        $data->scaffoldings_id = $request->scaffoldings_id;
        $data->return_days_id = $request->return_days_id;
        $data->borrowed_at = now();
        $data->save();
    
        return response()->json(['message' => 'Data Successfully Saved']);
    }
}
