<?php
use App\Http\Controllers\Api\RusunawaController as C;
use Illuminate\Support\Facades\Route;
Route::post('register',[C::class,'register'])->middleware('throttle:10,1');
Route::post('login',[C::class,'login'])->middleware('throttle:10,1');
Route::middleware(['auth:sanctum','throttle:60,1'])->group(function(){
    Route::post('logout',[C::class,'logout']);
    Route::get('profile',[C::class,'profile']);
    Route::put('profile',[C::class,'updateProfile']);
    Route::put('profile/password',[C::class,'password']);
    Route::get('rooms',[C::class,'rooms']);
    Route::get('rooms/{id}',[C::class,'room']);
    Route::post('rooms',[C::class,'createRoom']);
    Route::put('rooms/{id}',[C::class,'updateRoom']);
    Route::delete('rooms/{id}',[C::class,'deleteRoom']);
    Route::get('bookings',[C::class,'bookings']);
    Route::get('bookings/{id}',[C::class,'booking']);
    Route::post('bookings',[C::class,'createBooking']);
    Route::patch('bookings/{id}/cancel',[C::class,'cancelBooking']);
    Route::get('favorites',[C::class,'favorites']);
    Route::post('favorites',[C::class,'addFavorite']);
    Route::delete('favorites/{id}',[C::class,'removeFavorite']);
    Route::get('notifications',[C::class,'notifications']);
    Route::patch('notifications/read-all',[C::class,'readAll']);
    Route::patch('notifications/{id}/read',[C::class,'readNotification']);
});