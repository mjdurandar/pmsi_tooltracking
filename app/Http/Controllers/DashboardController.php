<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ProjectSite;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\BorrowTools;
use App\Models\BuyTools;
use Carbon\Carbon;
use App\Models\PowerTools;
use App\Models\Scaffolding;
use App\Models\ToolsAndEquipment;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB; 
use App\Models\AdminHistory;
use App\Models\ReturnDays;

class DashboardController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }

    public function productStocks()
    {
        // Count the occurrences of each product name
        $productCounts = ToolsAndEquipment::leftjoin('products', 'products.id', '=', 'tools_and_equipment.product_id')
            ->select(DB::raw("SUBSTRING_INDEX(products.brand, ' ', 1) as first_word"), DB::raw('COUNT(*) as count'))
            ->groupBy('first_word')
            ->get();
        
        // Prepare the data for the chart
        $labels = $productCounts->pluck('first_word');
        $values = $productCounts->pluck('count');
    
        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    public function supplierCount()
    {
        // Count the occurrences of each supplier
        $supplierCounts = Supplier::select('name', DB::raw('COUNT(*) as count'))
            ->groupBy('name')
            ->get();
    
        // Prepare the data for the chart
        $labels = $supplierCounts->pluck('name');
        $values = $supplierCounts->pluck('count');

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }    
    
    public function statusCount()
    {
        // Count the occurrences of status 'Borrowed'
        $borrowedCount = ToolsAndEquipment::where('status', 'For Borrowing')->count();

        // Count the occurrences of status 'Selling'
        $sellingCount = ToolsAndEquipment::where('status', 'For Sale')->count();


        return response()->json([
            'borrowedCount' => $borrowedCount,
            'sellingCount' => $sellingCount
        ]);
    }

    public function masterdataCount(){
        // Count the occurrences of each category of data
        $toolsandEquipmentCounts = ToolsAndEquipment::count();
        $returnDayCount = ReturnDays::count();
        $userCount = User::count();
        $categoryCount = Category::count();

        // Prepare the data for the bar chart
        $labels = ['Tools and Equipment', 'Return Days', 'Users', 'Category'];
        $values = [$toolsandEquipmentCounts, $returnDayCount, $userCount ,$categoryCount];

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    // public function balanceData(){{
    //     $user_id = Auth::id();
    //     $user = User::findOrFail($user_id);
    //     $balance = $user->balance; 
    //     $balance = User::latest()->value('balance'); // Assuming 'amount' is the column storing the balance

    //     // Return the balance data as a JSON response
    //     return response()->json(['balance' => $balance]);
    // }}
    
}
