<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IconController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Api\V1\FrontendController;
use App\Http\Controllers\Api\V1\Auth\AgencyController;
use App\Http\Controllers\Api\V1\AgencyServiceController;
use App\Http\Controllers\Api\V1\Auth\CustomerController;
use App\Http\Controllers\Api\V1\ServiceFacilityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// V1 Base Route.
Route::any('/', [FrontendController::class, "index"])->name("index");

// Guest routes
Route::group(['prefix' => 'auth', "middleware" => "guest"], function () {

    // auth default route
    Route::get('/', [FrontendController::class, "auth"])->name("auth");


    // Customer Routes
    Route::group(['prefix' => 'customer', 'as' => 'customer.', "controller" => CustomerController::class], function () {
        // guest route
        Route::middleware(['customer:false'])->group(function () {
            Route::post('/login', 'login')->name("login");
            Route::post('/register', 'register')->name("register");
        });

        // authorization route
        Route::middleware(['customer'])->group(function () {
            Route::get('/', 'customer')->name("customer");
            Route::post('/update', 'update')->name("update");
            Route::post('/update/password', 'password')->name("password");
            Route::post('/deactivate', 'deactivate')->name("deactivate");
            Route::post('/logout', 'logout')->name("logout");
        });
    });

    // Agency Routes
    Route::group(['prefix' => 'agency', 'as' => 'agency.', "controller" => AgencyController::class], function () {
        // guest route
        Route::middleware(['agency:false'])->group(function () {
            Route::post('/login', 'login')->name("login");
            Route::post('/register', 'register')->name("register");
        });

        // authorization route
        Route::middleware(['agency'])->group(function () {
            Route::get('/', 'agency')->name("agency");
            Route::post('/update', 'update')->name("update");
            Route::post('/update/password', 'password')->name("password");
            Route::post('/deactivate', 'deactivate')->name("deactivate");
            Route::post('/logout', 'logout')->name("logout");
        });
    });
});


// authorization route
Route::group(['prefix' => 'agency/service', 'as' => 'agency.service', 'middleware' => 'agency'], function () {
    // service route
    Route::get('/', [AgencyServiceController::class, 'index'])->name("index");
    Route::get('/details/{service}', [AgencyServiceController::class, 'details'])->name("details");
    Route::post('/register', [AgencyServiceController::class, 'register'])->name("register");
    Route::post('/update/{service}', [AgencyServiceController::class, 'update'])->name("update");
    Route::post('/delete/{service}', [AgencyServiceController::class, 'destroy'])->name("delete");
});

// Services routes
Route::resource('service', ServiceController::class)->only(["index"]);
Route::group(["as" => "service.", 'prefix' => 'service', "controller" => ServiceController::class], function () {
    Route::get('/details/{service}', 'details')->name("details");
    Route::get('/popular', 'popular')->name("popular");
    Route::get('/recommended', 'recommended')->name("recommended");
    // Route::post('/register', 'register')->name("register");
    // Route::post('/update', 'update')->name("update");
    // Route::post('/review/{service}', 'review')->middleware("customer")->name("review");


    // Facility routes
    Route::post('/facilities/register', [ServiceFacilityController::class, 'register'])->name("facilities.register");
    Route::post('/facilities/delete/{facility}', [ServiceFacilityController::class, 'delete'])->name("facilities.delete");
});


// Categories routes
Route::group(["as" => "categories.", "controller" => CategoryController::class], function () {
    Route::resource('categories', CategoryController::class)->only(['index', 'show']);
});

// Icons routes
Route::group(["as" => "icons.", "controller" => IconController::class], function () {
    Route::resource('icons', IconController::class)->only(['index', 'show']);
});
