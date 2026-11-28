<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::prefix("admin")->name("admin.")->group(function () { Route::get("/login", [\App\Http\Controllers\Admin\AuthController::class, "showLogin"])->name("login"); Route::post("/login", [\App\Http\Controllers\Admin\AuthController::class, "login"])->middleware("throttle:5,1")->name("login.submit"); Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () { Route::get("/", [\App\Http\Controllers\Admin\DashboardController::class, "index"])->name("dashboard"); Route::post("/logout", [\App\Http\Controllers\Admin\AuthController::class, "logout"])->name("logout"); }); });
Route::prefix("admin")->name("admin.")->middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () { Route::resource("rooms", \App\Http\Controllers\Admin\RoomController::class)->except(["show"]); });
Route::prefix("admin")->name("admin.")->middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () { Route::resource("students", \App\Http\Controllers\Admin\StudentController::class)->only(["index","show"]); });
Route::prefix("admin")->name("admin.")->middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () { Route::resource("bookings", \App\Http\Controllers\Admin\BookingController::class)->only(["index","show","update"]); });
