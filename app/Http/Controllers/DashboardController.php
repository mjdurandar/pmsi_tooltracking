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
use App\Models\Sales;

class DashboardController extends Controller
{
    public function index() {
        return view('admin.dashboard');
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

    public function salesData()
    {
        // Fetch sales data from the database, ordered by creation date
        $sales = Sales::orderBy('created_at', 'asc')->get();
    
        // Initialize arrays to store labels and values for the chart
        $labels = [];
        $values = [];
    
        // Iterate through each sale record
        foreach ($sales as $sale) {
            // Extract the relevant data for the chart
            $labels[] = $sale->created_at->format('Y-m-d'); // Format the date for labeling
            $values[] = $sale->total_price; // Use total_price for the chart data
        }
    
        // Return the data as a JSON response
        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }
    
    
}
