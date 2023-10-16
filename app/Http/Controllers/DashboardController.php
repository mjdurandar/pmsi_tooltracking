<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ProjectSite;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\BorrowTools;
use App\Models\BuyTools;

class DashboardController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }

    public function counts() {
        $projectCount = ProjectSite::count();
        $unitCount = Unit::count();
        $categoryCount = Category::count();
        $supplierCount = Supplier::count();
        $borrowedCount = BorrowTools::count();
        $buyingCount = BuyTools::count();
        return response()->json([ 'projectCount' => $projectCount, 'unitCount' => $unitCount, 'categoryCount' => $categoryCount, 'supplierCount' => $supplierCount, 'borrowedCount' => $borrowedCount, 'buyingCount' => $buyingCount ]);
    }
}
