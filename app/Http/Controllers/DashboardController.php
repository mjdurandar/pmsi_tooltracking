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
        $borrowedCount = AdminHistory::where('status', 'Borrowing')->count();

        // Count the occurrences of status 'Selling'
        $sellingCount = AdminHistory::where('status', 'Selling')->count();


        return response()->json([
            'borrowedCount' => $borrowedCount,
            'sellingCount' => $sellingCount
        ]);
    }

    public function masterdataCount(){
        // Count the occurrences of each category of data
        $projectSiteCount = ProjectSite::count();
        $returnDayCount = ReturnDays::count();
        $categoryCount = Category::count();
        $userCount = User::count();
        $unitCount = Unit::count();

        // Prepare the data for the bar chart
        $labels = ['Project Sites', 'Return Days', 'Categories', 'Users', 'Units'];
        $values = [$projectSiteCount, $returnDayCount, $categoryCount, $userCount, $unitCount];

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
    
    public function counts() {
        //Get Balance
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $balance = $user->balance; 
        
        // $projectCount = ProjectSite::count();
        // $unitCount = Unit::count();
        // $categoryCount = Category::count();
        // $supplierCount = Supplier::count();
        // $borrowedCount = BorrowTools::count();

        // $buyingCount = BuyTools::count();

        $salesCount = PowerTools::where('power_tools.is_sold_out', true)
                                ->whereDate('power_tools.sales_date', '=', now()->format('Y-m-d')) 
                                ->selectRaw('DATE(power_tools.sales_date) as date, SUM(power_tools.price) as total')
                                ->groupBy('date')
                                ->get();

        $borrowedCount = BorrowTools::whereDate('borrowed_at', Carbon::today())
                                    ->selectRaw('DATE(borrow_tools.borrowed_at) as date, COUNT(borrow_tools.borrowed_at) as total')
                                    ->groupBy('date')
                                    ->get();
        // dd($salesCount);

        // $totalPerDay = BuyTools::leftjoin('power_tools', 'power_tools.id', '=', 'buy_tools.power_tools_id')
        //                     ->selectRaw('DATE(created_at) as date, sum(price) as total')
        //                     ->groupBy('created_at')
        //                     ->select('power_tools.*', 'power_tools.price as price')
        //                     ->sum('price');

        // $powertoolsCounts = PowerTools::count();
        // $scaffoldingCounts = Scaffolding::count();
                                                                      
        // return response()->json([ 'projectCount' => $projectCount,
        //                         'unitCount' => $unitCount, 
        //                         'categoryCount' => $categoryCount, 
        //                         'supplierCount' => $supplierCount, 
        //                         'borrowedCount' => $borrowedCount,
        //                         'buyingCount' => $buyingCount,
        //                         'totalPerDay' => $totalPerDay,
        //                         'salesCount' => $salesCount,
        //                         'powertoolsCounts' => $powertoolsCounts,
        //                         'scaffoldingCounts' => $scaffoldingCounts
        //                         ]);

        return response()->json([ 
                                    'salesCount' => $salesCount,
                                    'borrowedCount' => $borrowedCount,
                                    'balance' => $balance
                                ]);
    }
}
