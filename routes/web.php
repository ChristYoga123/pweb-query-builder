<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\TransactionController;
use Illuminate\Support\Facades\Route;

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

// User
Route::get('/', [HomeController::class, "index"]);
Route::middleware("auth")->group(function () {
    Route::get("/show/{venue}", [HomeController::class, "show"])->name("venue.show");
    Route::get("/show/{id}/api", [HomeController::class, "show_api"]);
    Route::get("/payment", [TransactionController::class, "midtransCallback"]); # respons view snap
    Route::post("/payment", [TransactionController::class, "midtransCallback"]); # request via api
    Route::post("/transaction/{venue}", [TransactionController::class, "store"])->name("transaction.store");
});
Route::get("/success", [TransactionController::class, "success"])->name("transaction.success.index");
// oAuth
Route::get("login-google", [UserAuthController::class, "google"])->name("home.login.google.index");
Route::get("/auth/google/callback", [UserAuthController::class, "handleProviderCallback"]);
Route::post("logout", [UserAuthController::class, "logout"])->name("logout");

// Admin
// Non Auth
Route::prefix("admin")->middleware("guest_admin")->name("admin.")->group(function () {
    // Login
    Route::get("login", [AuthController::class, "index"])->name("login.index");
    Route::post("login", [AuthController::class, "login"])->name("login");
});

// Auth
Route::prefix("admin")->middleware("auth_admin")->name("admin.")->group(function () {
    // Login
    Route::post("logout", [AuthController::class, "logout"])->name("logout");
    // Dashboard
    Route::get("/", [DashboardController::class, "index"])->name("dashboard.index");
    // Venue
    Route::resource("venue", VenueController::class);
});
