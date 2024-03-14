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
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }

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
