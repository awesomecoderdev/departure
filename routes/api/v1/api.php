<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IconController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Api\V1\FrontendController;
use App\Http\Controllers\Api\V1\Auth\GuideController;
use App\Http\Controllers\Api\V1\AgencyGuideController;
use App\Http\Controllers\Api\V1\Auth\AgencyController;
use App\Http\Controllers\Api\v1\GuideServiceController;
use App\Http\Controllers\Api\v1\AgencyBookingController;
use App\Http\Controllers\Api\V1\AgencyServiceController;
use App\Http\Controllers\Api\V1\Auth\CustomerController;
use App\Http\Controllers\Api\V1\CustomerBookingController;
use App\Http\Controllers\Api\v1\GuideServiceFacilityController;
use App\Http\Controllers\Api\V1\AgencyServiceFacilityController;

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

            // wishlist route
            Route::get('/wishlist', [WishlistController::class, 'index'])->name("wishlist.index");
            Route::post('/wishlist/register', [WishlistController::class, 'register'])->name("wishlist.register");
            Route::post('/wishlist/delete', [WishlistController::class, 'destroy'])->name("wishlist.destroy");

            // bookings route
            Route::get('/booking', [CustomerBookingController::class, "booking"])->name("booking");
            Route::post('/booking/register', [CustomerBookingController::class, "register"])->name("booking.register");
            Route::post('/booking/update', [CustomerBookingController::class, "change"])->name("booking.change");
            Route::get('/booking/details/{booking}', [CustomerBookingController::class, "details"])->name("booking.details");
            Route::get('/booking/calculate', [CustomerBookingController::class, "calculate"])->name("booking.calculate");
        });
    });

    // Guide Routes
    Route::group(['prefix' => 'guide', 'as' => 'guide.', "controller" => GuideController::class], function () {
        // guest route
        Route::middleware(['guide:false'])->group(function () {
            Route::post('/login', 'login')->name("login");
            Route::post('/register', 'register')->name("register");
        });

        // authorization route
        Route::middleware(['guide'])->group(function () {
            Route::get('/', 'guide')->name("guide");
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

            // bookings route
            Route::get('/booking', [AgencyBookingController::class, "booking"])->name("booking");
        });
    });
});

// agency routes
Route::group(['prefix' => 'agency', 'as' => 'agency.', 'middleware' => "agency"], function () {

    // services route
    Route::group(['prefix' => 'service', 'as' => 'service'], function () {
        // service route
        Route::get('/', [AgencyServiceController::class, 'index'])->name("index");
        Route::get('/details/{service}', [AgencyServiceController::class, 'details'])->name("details");
        Route::post('/register', [AgencyServiceController::class, 'register'])->name("register");
        Route::post('/update/{service}', [AgencyServiceController::class, 'update'])->name("update");
        Route::post('/delete/{service}', [AgencyServiceController::class, 'destroy'])->name("delete");
        Route::post('/thumbnail/delete/{service}', [AgencyServiceController::class, 'thumbnail'])->name("thumbnail.delete");

        // Facility routes
        Route::post('/facilities/register', [AgencyServiceFacilityController::class, 'register'])->name("facilities.register");
        Route::post('/facilities/delete/{facility}', [AgencyServiceFacilityController::class, 'destroy'])->name("facilities.delete");
    });

    // guide route
    Route::group(['prefix' => 'guide', 'as' => 'guide.', 'middleware' => ["agency"], "controller" => AgencyGuideController::class], function () {
        Route::get('/', 'guide')->name("guide");
        Route::get('/details/{guide}', 'details')->name("details");
        Route::post('/register', 'register')->name("register");
        Route::post('/update/{guide}', 'update')->name("update");
        Route::post('/delete/{guide}', 'delete')->name("delete");
    });
});

// guide routes
Route::group(['prefix' => 'guide', 'as' => 'guide.', 'middleware' => "guide"], function () {

    // services route
    Route::group(['prefix' => 'service', 'as' => 'service'], function () {
        // service route
        Route::get('/', [GuideServiceController::class, 'index'])->name("index");
        Route::get('/details/{service}', [GuideServiceController::class, 'details'])->name("details");
        Route::post('/register', [GuideServiceController::class, 'register'])->name("register");
        Route::post('/update/{service}', [GuideServiceController::class, 'update'])->name("update");
        Route::post('/delete/{service}', [GuideServiceController::class, 'destroy'])->name("delete");
        Route::post('/thumbnail/delete/{service}', [GuideServiceController::class, 'thumbnail'])->name("thumbnail.delete");

        // Facility routes
        Route::post('/facilities/register', [GuideServiceFacilityController::class, 'register'])->name("facilities.register");
        Route::post('/facilities/delete/{facility}', [GuideServiceFacilityController::class, 'destroy'])->name("facilities.delete");
    });
});

// Services routes
Route::resource('service', ServiceController::class)->only(["index"]);
Route::group(["as" => "service.", 'prefix' => 'service', "controller" => ServiceController::class], function () {
    Route::get('/details/{service}', 'details')->name("details");
    Route::get('/popular', 'popular')->name("popular");
    Route::get('/recommended', 'recommended')->name("recommended");
    Route::get('/search', 'search')->name("search");

    // Route::post('/register', 'register')->name("register");
    // Route::post('/update', 'update')->name("update");
    // Route::post('/review/{service}', 'review')->middleware("customer")->name("review");
});

// Categories routes
Route::group(["as" => "categories.", "controller" => CategoryController::class], function () {
    Route::resource('categories', CategoryController::class)->only(['index', 'show']);
});

// Icons routes
Route::group(["as" => "icons.", "controller" => IconController::class], function () {
    Route::resource('icons', IconController::class)->only(['index', 'show']);
});
