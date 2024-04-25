<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Auth::routes();


Route::get('/unauthorized', function () {
    return view('unauthorized');
});


Route::group(['middleware' => 'auth'], function(){


    Route::get('/', function () {
        return view('home');
    });
    
    Route::get('home', function () {
        return view('home');
    });

    // Route::get('/powertools/show',[App\Http\Controllers\PowerToolsController::class, 'show']);

    // Route::get('/buyinghistory',[App\Http\Controllers\BuyToolsController::class, 'history']);
    // Route::get('/buyinghistory/showHistory',[App\Http\Controllers\BuyToolsController::class, 'showHistory']);

    // Route::get('/borrowedhistory',[App\Http\Controllers\BorrowToolsController::class, 'history']);
    // Route::get('/borrowedhistory/showHistory',[App\Http\Controllers\BorrowToolsController::class, 'showHistory']);
    // Route::get('/',[App\Http\Controllers\DashboardController::class, 'index']);
    //ADD BALANCE
    Route::get('/add-balance',[App\Http\Controllers\AddBalanceController::class, 'index']);
    Route::get('/add-balance/show',[App\Http\Controllers\AddBalanceController::class, 'show']);
    Route::post('/add-balance/store',[App\Http\Controllers\AddBalanceController::class, 'store']);

    Route::middleware(['CheckRole:admin'])->group(function() {
        // DASHBOARD
        Route::get('/dashboard',[App\Http\Controllers\DashboardController::class, 'index']);
        Route::get('/dashboard/productStocks',[App\Http\Controllers\DashboardController::class, 'productStocks']);
        Route::get('/dashboard/statusCount',[App\Http\Controllers\DashboardController::class, 'statusCount']);
        Route::get('/dashboardCount/counts',[App\Http\Controllers\DashboardController::class, 'counts']);
        Route::get('/dashboard/balanceData',[App\Http\Controllers\DashboardController::class, 'balanceData']);
        Route::get('/dashboard/supplierCount',[App\Http\Controllers\DashboardController::class, 'supplierCount']);
        Route::get('/dashboard/masterdataCount',[App\Http\Controllers\DashboardController::class, 'masterdataCount']);

        // PROJECT SITE
        Route::get('/project-site',[App\Http\Controllers\ProjectSiteController::class, 'index']);
        Route::get('/project-site/show',[App\Http\Controllers\ProjectSiteController::class, 'show']);
        Route::post('/project-site/store',[App\Http\Controllers\ProjectSiteController::class, 'store']);
        Route::get('/project-site/edit/{projectsite}',[App\Http\Controllers\ProjectSiteController::class, 'edit']);
        Route::get('/project-site/destroy/{projectsite}',[App\Http\Controllers\ProjectSiteController::class, 'destroy']);

        // RETURN DAYS 
        Route::get('/returndays',[App\Http\Controllers\ReturnDaysController::class, 'index']);
        Route::get('/returndays/show',[App\Http\Controllers\ReturnDaysController::class, 'show']);
        Route::post('/returndays/store',[App\Http\Controllers\ReturnDaysController::class, 'store']);
        Route::get('/returndays/edit/{returndays}',[App\Http\Controllers\ReturnDaysController::class, 'edit']);
        Route::get('/returndays/destroy/{returndays}',[App\Http\Controllers\ReturnDaysController::class, 'destroy']);

        // CATEGORY 
        Route::get('/category',[App\Http\Controllers\CategoryController::class, 'index']);
        Route::get('/category/show',[App\Http\Controllers\CategoryController::class, 'show']);
        Route::post('/category/store',[App\Http\Controllers\CategoryController::class, 'store']);
        Route::get('/category/edit/{category}',[App\Http\Controllers\CategoryController::class, 'edit']);
        Route::get('/category/destroy/{category}',[App\Http\Controllers\CategoryController::class, 'destroy']);

        // SUPPLIER 
        Route::get('/supplier',[App\Http\Controllers\SupplierController::class, 'index']);
        Route::get('/supplier/show',[App\Http\Controllers\SupplierController::class, 'show']);
        Route::post('/supplier/filterData',[App\Http\Controllers\SupplierController::class, 'filterData']);
        Route::post('/supplier/purchaseProduct',[App\Http\Controllers\SupplierController::class, 'purchaseProduct']);

        // POWERTOOLS 
        Route::get('/powertools',[App\Http\Controllers\PowerToolsController::class, 'index']);
        Route::get('/powertools/show',[App\Http\Controllers\PowerToolsController::class, 'show']);
        Route::post('/powertools/store',[App\Http\Controllers\PowerToolsController::class, 'store']);
        Route::post('/powertools/filterData',[App\Http\Controllers\PowerToolsController::class, 'filterData']);
        Route::get('/powertools/releaseProduct/{toolsAndEquipment}',[App\Http\Controllers\PowerToolsController::class, 'releaseProduct']);
        Route::post('/powertools/updateProduct',[App\Http\Controllers\PowerToolsController::class, 'updateProduct']);
        Route::post('/powertools/cancelProduct',[App\Http\Controllers\PowerToolsController::class, 'cancelProduct']);
        Route::get('/powertools/destroy/{powertools}',[App\Http\Controllers\PowerToolsController::class, 'destroy']);

        // USER
        Route::get('/users',[App\Http\Controllers\UsersController::class, 'index']);
        Route::get('/users/show',[App\Http\Controllers\UsersController::class, 'show']);
        Route::get('/users/showBuyingHistory/{id}',[App\Http\Controllers\UsersController::class, 'showBuyingHistory']);
        Route::post('/users/store',[App\Http\Controllers\UsersController::class, 'store']);
        Route::get('/users/edit/{users}',[App\Http\Controllers\UsersController::class, 'edit']);
        Route::get('/users/destroy/{users}',[App\Http\Controllers\UsersController::class, 'destroy']);
        Route::post('/users/filterData',[App\Http\Controllers\UsersController::class, 'filterData']);

        //ORDER
        Route::get('/order',[App\Http\Controllers\OrderController::class, 'index']);
        Route::get('/order/show',[App\Http\Controllers\OrderController::class, 'show']);
        Route::post('/order/store',[App\Http\Controllers\OrderController::class, 'store']);

        //PRODUCT HISTORY
        Route::get('/product-history',[App\Http\Controllers\ProductHistoryController::class, 'index']);
        Route::get('/product-history/show',[App\Http\Controllers\ProductHistoryController::class, 'show']);
        Route::post('/product-history/viewHistory/',[App\Http\Controllers\ProductHistoryController::class, 'viewHistory']);

        //CANCEL HISTORY
        Route::get('/cancel-history',[App\Http\Controllers\CancelHistoryController::class, 'index']);
        Route::get('/cancel-history/show',[App\Http\Controllers\CancelHistoryController::class, 'show']);
        Route::post('/cancel-history/viewHistory/',[App\Http\Controllers\CancelHistoryController::class, 'viewHistory']);

    });
   
    Route::middleware(['CheckRole:user'])->group(function() {

        //QR REDIRECT
        Route::get('/qr/{id}',[App\Http\Controllers\QRController::class, 'index']);
        // Route::get('/qr/show/',[App\Http\Controllers\QRController::class, 'show']);

        // Route::get('/',[App\Http\Controllers\BuyToolsController::class, 'index']);
        Route::get('/buytools',[App\Http\Controllers\BuyToolsController::class, 'index']);
        Route::get('/buytools/show',[App\Http\Controllers\BuyToolsController::class, 'show']);
        Route::post('/buytools/store',[App\Http\Controllers\BuyToolsController::class, 'store']);
        Route::get('/buytools/edit/{buytools}',[App\Http\Controllers\BuyToolsController::class, 'edit']);
        Route::get('/buytools/destroy/{buytools}',[App\Http\Controllers\BuyToolsController::class, 'destroy']);
        Route::post('/buytools/filterData',[App\Http\Controllers\BuyToolsController::class, 'filterData']);

        Route::get('/borrowtools',[App\Http\Controllers\BorrowToolsController::class, 'index']);
        Route::get('/borrowtools/show',[App\Http\Controllers\BorrowToolsController::class, 'show']);
        Route::post('/borrowtools/store',[App\Http\Controllers\BorrowToolsController::class, 'store']);
        Route::get('/borrowtools/edit/{borrowtools}',[App\Http\Controllers\BorrowToolsController::class, 'edit']);
        Route::get('/borrowtools/destroy/{borrowtools}',[App\Http\Controllers\BorrowToolsController::class, 'destroy']);
        Route::post('/borrowtools/filterData',[App\Http\Controllers\BorrowToolsController::class, 'filterData']); 

        //BUYING HISTORY
        Route::get('/buyinghistory',[App\Http\Controllers\BuyingHistoryController::class, 'index']);
        Route::get('/buyinghistory/show',[App\Http\Controllers\BuyingHistoryController::class, 'show']);
        Route::post('/buyinghistory/filterData',[App\Http\Controllers\BuyingHistoryController::class, 'filterData']);

        //BORROW HISTORY
        Route::get('/borrowedhistory',[App\Http\Controllers\BorrowHistoryController::class, 'index']);
        Route::get('/borrowedhistory/show',[App\Http\Controllers\BorrowHistoryController::class, 'show']);
        Route::post('/borrowedhistory/filterData',[App\Http\Controllers\BorrowHistoryController::class, 'filterData']);

        //DELIVERY
        Route::get('/delivery',[App\Http\Controllers\DeliveryController::class, 'index']);
        Route::get('/delivery/show',[App\Http\Controllers\DeliveryController::class, 'show']);
        Route::post('/delivery/cancelOrder',[App\Http\Controllers\DeliveryController::class, 'cancelOrder']);
        Route::get('/delivery/destroy/{delivery}', [App\Http\Controllers\DeliveryController::class, 'destroy']);
    });

    Route::middleware(['CheckRole:supplier'])->group(function() {

        Route::get('profile', [App\Http\Controllers\ProfileController::class, 'index']);
        Route::get('profile/show', [App\Http\Controllers\ProfileController::class, 'show']);
        Route::post('profile/store', [App\Http\Controllers\ProfileController::class, 'store']);

        Route::get('products', [App\Http\Controllers\ProductController::class, 'index']);
        Route::get('products/show', [App\Http\Controllers\ProductController::class, 'show']);
        Route::post('products/store', [App\Http\Controllers\ProductController::class, 'store']);
        Route::get('products/edit/{product}', [App\Http\Controllers\ProductController::class, 'edit']);
        Route::get('products/destroy/{product}', [App\Http\Controllers\ProductController::class, 'destroy']);
    });

});

