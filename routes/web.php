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

    Route::get('/powertools/show',[App\Http\Controllers\PowerToolsController::class, 'show']);

    Route::get('/buyinghistory',[App\Http\Controllers\BuyToolsController::class, 'history']);
    Route::get('/buyinghistory/showHistory',[App\Http\Controllers\BuyToolsController::class, 'showHistory']);

    Route::get('/borrowedhistory',[App\Http\Controllers\BorrowToolsController::class, 'history']);
    Route::get('/borrowedhistory/showHistory',[App\Http\Controllers\BorrowToolsController::class, 'showHistory']);

    Route::middleware(['CheckRole:admin'])->group(function() {
        // DASHBOARD
        Route::get('/dashboard',[App\Http\Controllers\DashboardController::class, 'index']);
        Route::get('/dashboardCount/counts',[App\Http\Controllers\DashboardController::class, 'counts']);


        // PROJECY SITE
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

        // UNIT 
        Route::get('/unit',[App\Http\Controllers\UnitController::class, 'index']);
        Route::get('/unit/show',[App\Http\Controllers\UnitController::class, 'show']);
        Route::post('/unit/store',[App\Http\Controllers\UnitController::class, 'store']);
        Route::get('/unit/edit/{unit}',[App\Http\Controllers\UnitController::class, 'edit']);
        Route::get('/unit/destroy/{unit}',[App\Http\Controllers\UnitController::class, 'destroy']);

        // SUPPLIER 
        Route::get('/supplier',[App\Http\Controllers\SupplierController::class, 'index']);
        Route::get('/supplier/show',[App\Http\Controllers\SupplierController::class, 'show']);
        Route::post('/supplier/store',[App\Http\Controllers\SupplierController::class, 'store']);
        Route::get('/supplier/edit/{supplier}',[App\Http\Controllers\SupplierController::class, 'edit']);
        Route::get('/supplier/destroy/{supplier}',[App\Http\Controllers\SupplierController::class, 'destroy']);

        // POWERTOOLS 
        Route::get('/powertools',[App\Http\Controllers\PowerToolsController::class, 'index']);
        Route::post('/powertools/store',[App\Http\Controllers\PowerToolsController::class, 'store']);
        Route::get('/powertools/edit/{powertools}',[App\Http\Controllers\PowerToolsController::class, 'edit']);
        Route::get('/powertools/destroy/{powertools}',[App\Http\Controllers\PowerToolsController::class, 'destroy']);

        // SCAFFOLDING 
        Route::get('/scaffolding',[App\Http\Controllers\ScaffoldingController::class, 'index']);
        Route::get('/scaffolding/show',[App\Http\Controllers\ScaffoldingController::class, 'show']);
        Route::post('/scaffolding/store',[App\Http\Controllers\ScaffoldingController::class, 'store']);
        Route::get('/scaffolding/edit/{scaffolding}',[App\Http\Controllers\ScaffoldingController::class, 'edit']);
        Route::get('/scaffolding/destroy/{scaffolding}',[App\Http\Controllers\ScaffoldingController::class, 'destroy']);

        // USER
        Route::get('/users',[App\Http\Controllers\UsersController::class, 'index']);
        Route::get('/users/show',[App\Http\Controllers\UsersController::class, 'show']);
        Route::post('/users/store',[App\Http\Controllers\UsersController::class, 'store']);
        Route::get('/users/edit/{users}',[App\Http\Controllers\UsersController::class, 'edit']);
        Route::get('/users/destroy/{users}',[App\Http\Controllers\UsersController::class, 'destroy']);

    });
   
    Route::middleware(['CheckRole:user'])->group(function() {
        Route::get('/buytools',[App\Http\Controllers\BuyToolsController::class, 'index']);
        Route::get('/buytools/show',[App\Http\Controllers\BuyToolsController::class, 'show']);
        Route::post('/buytools/store',[App\Http\Controllers\BuyToolsController::class, 'store']);
        Route::get('/buytools/edit/{buytools}',[App\Http\Controllers\BuyToolsController::class, 'edit']);
        Route::get('/buytools/destroy/{buytools}',[App\Http\Controllers\BuyToolsController::class, 'destroy']);

        Route::get('/borrowtools',[App\Http\Controllers\BorrowToolsController::class, 'index']);
        Route::get('/borrowtools/show',[App\Http\Controllers\BorrowToolsController::class, 'show']);
        Route::post('/borrowtools/store',[App\Http\Controllers\BorrowToolsController::class, 'store']);
        Route::get('/borrowtools/edit/{borrowtools}',[App\Http\Controllers\BorrowToolsController::class, 'edit']);
        Route::get('/borrowtools/destroy/{borrowtools}',[App\Http\Controllers\BorrowToolsController::class, 'destroy']);

        Route::get('/delivery',[App\Http\Controllers\DeliveryController::class, 'index']);
        Route::get('/delivery/show',[App\Http\Controllers\DeliveryController::class, 'show']);
    });

});

