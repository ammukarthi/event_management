<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/register',[UserController::class,'register']);

Route::post('/login',[UserController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me',[UserController::class,'profile']);

    Route::post('/logout',[UserController::class,'logout']);

    Route::get('/events',[EventController::class,'events']);

    Route::get('/events/{id}',[EventController::class,'view']);
});

Route::middleware(['auth:sanctum','role:organizer'])->group(function () {

    Route::get('/orgEvents',[EventController::class,'organizerEvents']);

    Route::post('/events',[EventController::class,'addEvents']);

    Route::put('/events/{id}',[EventController::class,'editEvents']);

    Route::delete('/events/{id}',[EventController::class,'delete']);

    Route::post('/events/{event_id}/tickets',[EventController::class,'addTickets']);

    Route::put('/tickets/{id}',[EventController::class,'editTickets']);

    Route::delete('/tickets/{id}',[EventController::class,'ticketdelete']);

});

Route::middleware(['auth:sanctum','role:customer'])->group(function () {

    Route::post('/tickets/{ticket_id}/bookings',[BookingController::class,'bookTicket']);

    Route::get('/bookings',[BookingController::class,'bookingHistory']);

    Route::put('/bookings/{booking_id}/cancel',[BookingController::class,'cancel']);

    Route::post('/bookings/{booking_id}/payment',[BookingController::class,'paymentProcess']);

    Route::get('/payments/{id}',[BookingController::class,'paymentDetail']);

});
