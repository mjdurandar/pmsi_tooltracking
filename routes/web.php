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

Route::group(['middleware' => 'auth'], function(){

    Route::get('/', function () {
        return view('home');
    });
    
    Route::get('home', function () {
        return view('home');
    });

    // PROJECY SITE
    Route::get('/project-site',[App\Http\Controllers\ProjectSiteController::class, 'index']);
    Route::get('/project-site/show',[App\Http\Controllers\ProjectSiteController::class, 'show']);
    Route::post('/project-site/store',[App\Http\Controllers\ProjectSiteController::class, 'store']);
    Route::get('/project-site/edit/{projectsite}',[App\Http\Controllers\ProjectSiteController::class, 'edit']);
    Route::get('/project-site/destroy/{projectsite}',[App\Http\Controllers\ProjectSiteController::class, 'destroy']);

    // CATEGORY 
    Route::get('/category',[App\Http\Controllers\CategoryController::class, 'index']);
    Route::get('/category/show',[App\Http\Controllers\CategoryController::class, 'show']);
    Route::post('/category/store',[App\Http\Controllers\CategoryController::class, 'store']);
    Route::get('/category/edit/{category}',[App\Http\Controllers\CategoryController::class, 'edit']);
    Route::get('/category/destroy/{category}',[App\Http\Controllers\CategoryController::class, 'destroy']);

});

