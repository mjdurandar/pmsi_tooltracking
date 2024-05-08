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
    
    Route::get('/qr/{id}',[App\Http\Controllers\QRController::class, 'index']);
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
        Route::post('/supplier/getId', [App\Http\Controllers\SupplierController::class, 'getId']);

        // POWERTOOLS 
        Route::get('/powertools',[App\Http\Controllers\PowerToolsController::class, 'index']);
        Route::get('/powertools/show',[App\Http\Controllers\PowerToolsController::class, 'show']);
        Route::post('/powertools/store',[App\Http\Controllers\PowerToolsController::class, 'store']);
        Route::post('/powertools/filterData',[App\Http\Controllers\PowerToolsController::class, 'filterData']);
        Route::post('/powertools/releasedProduct',[App\Http\Controllers\PowerToolsController::class, 'releasedProduct']);

        // USER
        Route::get('/users',[App\Http\Controllers\UsersController::class, 'index']);
        Route::get('/users/show',[App\Http\Controllers\UsersController::class, 'show']);
        Route::get('/users/showBuyingHistory/{id}',[App\Http\Controllers\UsersController::class, 'showBuyingHistory']);
        Route::post('/users/store',[App\Http\Controllers\UsersController::class, 'store']);
        Route::get('/users/edit/{users}',[App\Http\Controllers\UsersController::class, 'edit']);
        Route::get('/users/destroy/{users}',[App\Http\Controllers\UsersController::class, 'destroy']);
        Route::post('/users/filterData',[App\Http\Controllers\UsersController::class, 'filterData']);

        // ADMIN PRODUCTS
        Route::get('/admin-products',[App\Http\Controllers\AdminProductsController::class, 'index']);
        Route::get('/admin-products/show',[App\Http\Controllers\AdminProductsController::class, 'show']);
        Route::post('/admin-products/returnProduct',[App\Http\Controllers\AdminProductsController::class, 'returnProduct']);
        Route::get('/admin-returned-product',[App\Http\Controllers\AdminProductsController::class, 'returnedProductIndex']);
        Route::get('/admin-returned-product/returnedProductShow',[App\Http\Controllers\AdminProductsController::class, 'returnedProductShow']);
        Route::post('/admin-products/approvedProduct',[App\Http\Controllers\AdminProductsController::class, 'approvedProduct']);

        //TRACK ORDER
        Route::get('/track-order',[App\Http\Controllers\TrackOrderController::class, 'index']);
        Route::get('/track-order/show',[App\Http\Controllers\TrackOrderController::class, 'show']);
        Route::post('/track-order/cancelOrder',[App\Http\Controllers\TrackOrderController::class, 'cancelOrder']);

        //COMPLETED ORDER
        Route::get('/completed-order-admin',[App\Http\Controllers\CompletedOrderAdminController::class, 'index']);
        Route::get('/completed-order-admin/show',[App\Http\Controllers\CompletedOrderAdminController::class, 'show']);

        //CANCELED ORDER
        Route::get('/canceled-order',[App\Http\Controllers\CanceledOrderController::class, 'index']);
        Route::get('/canceled-order/show',[App\Http\Controllers\CanceledOrderController::class, 'show']);

        //ORDER
        Route::get('/order',[App\Http\Controllers\OrderController::class, 'index']);
        Route::get('/order/show',[App\Http\Controllers\OrderController::class, 'show']);
        Route::post('/order/store',[App\Http\Controllers\OrderController::class, 'store']);
        Route::post('/order/updateStatus',[App\Http\Controllers\OrderController::class, 'updateStatus']);
        //order complete
        Route::get('/complete-order-admin-product',[App\Http\Controllers\OrderController::class, 'completeOrderAdminIndex']);
        Route::get('/complete-order-admin-product/completeOrderAdminShow',[App\Http\Controllers\OrderController::class, 'completeOrderAdminShow']);
        //order cancel
        Route::get('/canceled-order-admin-product',[App\Http\Controllers\OrderController::class, 'canceledOrderAdminIndex']);
        Route::get('/canceled-order-admin-product/canceledOrderAdminShow',[App\Http\Controllers\OrderController::class, 'canceledOrderAdminShow']);


        //PRODUCT HISTORY
        Route::get('/product-history',[App\Http\Controllers\ProductHistoryController::class, 'index']);
        Route::get('/product-history/show',[App\Http\Controllers\ProductHistoryController::class, 'show']);
        Route::post('/product-history/viewHistory/',[App\Http\Controllers\ProductHistoryController::class, 'viewHistory']);

        //CANCEL HISTORY
        Route::get('/cancel-history',[App\Http\Controllers\CancelHistoryController::class, 'index']);
        Route::get('/cancel-history/show',[App\Http\Controllers\CancelHistoryController::class, 'show']);
        Route::post('/cancel-history/viewHistory/',[App\Http\Controllers\CancelHistoryController::class, 'viewHistory']);

        //RETURNED PRODUCT
        Route::get('/returned-product',[App\Http\Controllers\ReturnedProductController::class, 'index']);
        Route::get('/returned-product/show',[App\Http\Controllers\ReturnedProductController::class, 'show']);
        Route::post('/returned-product/damaged',[App\Http\Controllers\ReturnedProductController::class, 'damaged']);
        Route::post('/returned-product/store',[App\Http\Controllers\ReturnedProductController::class, 'store']);

        //DEFECTIVE PRODUCT
        Route::get('/defective-products',[App\Http\Controllers\DefectiveProductsController::class, 'index']);
        Route::get('/defective-products/show',[App\Http\Controllers\DefectiveProductsController::class, 'show']);
        Route::post('/defective-products/damaged',[App\Http\Controllers\DefectiveProductsController::class, 'damaged']);
        Route::post('/defective-products/store',[App\Http\Controllers\DefectiveProductsController::class, 'store']);

    });
   
    Route::middleware(['CheckRole:user'])->group(function() {

        //QR REDIRECT
        // Route::get('/qr/show/',[App\Http\Controllers\QRController::class, 'show']);

        // Route::get('/',[App\Http\Controllers\BuyToolsController::class, 'index']);
        Route::get('/buytools',[App\Http\Controllers\BuyToolsController::class, 'index']);
        Route::get('/buytools/show',[App\Http\Controllers\BuyToolsController::class, 'show']);
        Route::post('/buytools/buyTools',[App\Http\Controllers\BuyToolsController::class, 'buyTools']);
        Route::get('/buytools/edit/{buytools}',[App\Http\Controllers\BuyToolsController::class, 'edit']);
        Route::get('/buytools/destroy/{buytools}',[App\Http\Controllers\BuyToolsController::class, 'destroy']);
        Route::post('/buytools/filterData',[App\Http\Controllers\BuyToolsController::class, 'filterData']);

        //BORROW TOOLS
        Route::get('/borrowtools',[App\Http\Controllers\BorrowToolsController::class, 'index']);
        Route::get('/borrowtools/show',[App\Http\Controllers\BorrowToolsController::class, 'show']);
        Route::post('/borrowtools/borrowTools',[App\Http\Controllers\BorrowToolsController::class, 'borrowTools']);
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
        //COMPLETED ORDER
        Route::get('/completed-order-user',[App\Http\Controllers\DeliveryController::class, 'completedOrderUserIndex']);
        Route::get('/completed-order-user/completedOrderUserShow',[App\Http\Controllers\DeliveryController::class, 'completedOrderUserShow']);
        //canceled order
        Route::get('/canceled-order-user',[App\Http\Controllers\DeliveryController::class, 'canceledOrderUserIndex']);
        Route::get('/canceled-order-user/canceledOrderUserShow',[App\Http\Controllers\DeliveryController::class, 'canceledOrderUserShow']);

        //RETURN A PRODUCT
        Route::get('/return-product',[App\Http\Controllers\ReturnProductController::class, 'index']);
        Route::get('/return-product/show',[App\Http\Controllers\ReturnProductController::class, 'show']);
        Route::post('/return-product/store',[App\Http\Controllers\ReturnProductController::class, 'store']);

        //DAMAGED RETURN
        Route::get('/damaged-return',[App\Http\Controllers\DamagedReturnController::class, 'index']);
        Route::get('/damaged-return/show',[App\Http\Controllers\DamagedReturnController::class, 'show']);
        Route::post('/damaged-return/store',[App\Http\Controllers\DamagedReturnController::class, 'store']);


    });

    Route::middleware(['CheckRole:supplier'])->group(function() {

        Route::get('profile', [App\Http\Controllers\ProfileController::class, 'index']);
        Route::get('profile/show', [App\Http\Controllers\ProfileController::class, 'show']);
        Route::post('profile/store', [App\Http\Controllers\ProfileController::class, 'store']);

        //PRODUCTS AND CANCELED PRODUCTS

        Route::get('products', [App\Http\Controllers\ProductController::class, 'index']);
        Route::get('products/show', [App\Http\Controllers\ProductController::class, 'show']);
        Route::post('products/store', [App\Http\Controllers\ProductController::class, 'store']);
        Route::get('products/edit/{product}', [App\Http\Controllers\ProductController::class, 'edit']);
        Route::post('products/releasedProduct', [App\Http\Controllers\ProductController::class, 'releasedProduct']);
        Route::post('products/isRemoved', [App\Http\Controllers\ProductController::class, 'isRemoved']);
        Route::post('products/canceledProduct', [App\Http\Controllers\ProductController::class, 'canceledProduct']);
        Route::post('products/filterData', [App\Http\Controllers\ProductController::class, 'filterData']);

        Route::get('canceled-product', [App\Http\Controllers\CanceledProductController::class, 'index']);
        Route::get('canceled-product/show', [App\Http\Controllers\CanceledProductController::class, 'show']);
        Route::post('canceled-product/release', [App\Http\Controllers\CanceledProductController::class, 'release']);
        Route::post('canceled-product/filterData', [App\Http\Controllers\CanceledProductController::class, 'filterData']);

        //ORDERED PRODUCTS
        Route::get('ordered-products', [App\Http\Controllers\OrderedProductsController::class, 'index']);
        Route::get('ordered-products/show', [App\Http\Controllers\OrderedProductsController::class, 'show']);
        Route::get('completed-ordered-products', [App\Http\Controllers\OrderedProductsController::class, 'completedIndex']);
        Route::get('completed-ordered-products/completedShow', [App\Http\Controllers\OrderedProductsController::class, 'completedShow']);
        Route::get('canceled-ordered-products', [App\Http\Controllers\OrderedProductsController::class, 'canceledIndex']);
        Route::get('canceled-ordered-products/canceledShow', [App\Http\Controllers\OrderedProductsController::class, 'canceledShow']);
        Route::post('ordered-products/updateStatus', [App\Http\Controllers\OrderedProductsController::class, 'updateStatus']);

        //RETURNED PRODUCTS
        Route::get('returned-products-supplier', [App\Http\Controllers\ReturnedProductsSupplierController::class, 'index']);
        Route::get('returned-products-supplier/show', [App\Http\Controllers\ReturnedProductsSupplierController::class, 'show']);
        
    });

});

