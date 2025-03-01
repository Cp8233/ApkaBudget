<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Website\WebsiteController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::match(['get', 'post'], 'admin/login', [AuthController::class, 'login'])->name('admin.login');

Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');
});


Route::get('terms-and-conditions', [WebsiteController::class, 'terms_conditions']);
Route::get('contact-us', [WebsiteController::class, 'contact_us']);
Route::get('refund-policy', [WebsiteController::class, 'refund_policy']);
Route::get('privacy-policy', [WebsiteController::class, 'privacy_policy']);