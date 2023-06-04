<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VenueController;
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

Route::get('/', function () {
    return view('welcome');
});

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
