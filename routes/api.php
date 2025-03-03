<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommonController;

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

Route::get('countries', [ProviderController::class, 'countries']);
Route::post('states', [ProviderController::class, 'states']);
Route::post('cities', [ProviderController::class, 'cities']);

//Provider side login system
Route::post('provider_register', [AuthController::class, 'provider_register']);
Route::post('provider_login', [AuthController::class, 'provider_login']);

//User side login system
Route::post('user_login', [AuthController::class, 'user_login']);
Route::post('verify_otp', [AuthController::class, 'verify_otp']);

// Route::group(['middleware' => 'auth:sanctum'], function () {
Route::middleware(['api.auth'])->group(function () {
    
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('delete-account', [AuthController::class, 'deleteAccount']);

    //Provider side api's
    Route::get('identity_types', [ProviderController::class, 'identity_types']);
    Route::post('payment_status', [ProviderController::class, 'payment_status']);// payment karne ke baad ye api hit hogi
    Route::get('dashboard', [ProviderController::class, 'dashboard']);
    Route::get('bookings', [ProviderController::class, 'bookings']); //provider side all booking list
    Route::post('plans', [ProviderController::class, 'plans']);
    
    
    

    //User side Api's
    Route::post('save_location', [UserController::class, 'save_location']);
    Route::post('sub_categories', [UserController::class, 'sub_categories']);
    Route::post('services', [UserController::class, 'services']); //sub_subcategories and services and view_cart three includes
    Route::post('rate_card', [UserController::class, 'rate_card']);
    Route::post('add-to-cart', [UserController::class, 'addToCart']); // services add in cart
    Route::post('add_address', [UserController::class, 'add_address']); //add address
    Route::get('addresses', [UserController::class, 'addresses']); // address list
    Route::post('daily-slots', [UserController::class, 'getDailySlots']); // View Slots
    Route::post('checkout', [UserController::class, 'checkout']); // check out cart item subctegory wise
    Route::post('paymentstatus', [UserController::class, 'paymentstatus']);// check payment after booking
    Route::get('my_bookings', [UserController::class, 'my_bookings']); //booking list
    
    
    

    //Common Api's
    Route::get('categories', [CommonController::class, 'categories']);
    Route::get('notifications', [CommonController::class, 'notifications']); // all notiication list
    Route::get('transaction_history', [CommonController::class, 'transaction_history']);
    Route::get('profile', [CommonController::class, 'profile']);
    Route::post('edit_profile', [CommonController::class, 'edit_profile']);

});

Route::post('send-notification', [UserController::class, 'send_notification']);// check payment
