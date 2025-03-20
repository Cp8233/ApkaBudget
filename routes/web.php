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
    
    
     Route::get('categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::match(['get', 'post'], 'categories/add', [AdminController::class, 'add_categories'])->name('admin.add_categories');
    Route::match(['get','post'],'categories/edit/{id}', [AdminController::class, 'edit_categories'])->name('admin.edit_categories');
    Route::delete('categories/{id}/delete', [AdminController::class, 'delete_categories'])->name('admin.delete_categories');

    // SubCategory Route

    Route::get('{CategoryId}/sub-categories', [AdminController::class, 'subcategories'])->name('admin.subcategories');
    Route::match(['get','post'],'{CategoryId}/sub-categories/add', [AdminController::class, 'add_subcategories'])->name('admin.add_subcategories');
    Route::match(['get','post'],'{CategoryId}/sub-categories/edit/{id}', [AdminController::class, 'edit_subcategories'])->name('admin.edit_subcategories');
    Route::delete('{CategoryId}/sub-categories/delete/{id}', [AdminController::class, 'delete_subcategories'])->name('admin.delete_subcategories');

    //Country Route

    Route::get('countries', [AdminController::class, 'countries'])->name('admin.countries');
    Route::get('countries/add', [AdminController::class, 'add_countries'])->name('admin.add_countries');
    Route::get('countries/edit/{id}', [AdminController::class, 'edit_countries'])->name('admin.edit_countries');
    Route::get('countries/delete/{id}', [AdminController::class, 'delete_countries'])->name('admin.delete_countries');

    //stete Route

    Route::get('state', [AdminController::class, 'states'])->name('admin.states');
    Route::get('state/add', [AdminController::class, 'add_states'])->name('admin.add_states');
    Route::get('state/edit/{id}', [AdminController::class, 'edit_states'])->name('admin.edit_states');
    Route::get('state/delete/{id}', [AdminController::class, 'delete_states'])->name('admin.delete_states');

    //Users Route

    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::match(['get', 'post'], 'users/add', [AdminController::class, 'add_users'])->name('admin.add_users');
    Route::match(['get', 'post'], 'users/{id}/edit', [AdminController::class, 'edit_users'])->name('admin.edit_users');
    Route::delete('users/{id}/delete', [AdminController::class, 'delete_users'])->name('admin.delete_users');
    
    Route::get('users/{userId}/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
    Route::match(['get', 'post'], 'users/{userId}/bookings/create-booking', [AdminController::class, 'create_booking'])->name('admin.create_booking');

    Route::get('users/{userId}/addresses', [AdminController::class, 'addresses'])->name('admin.addresses');
    Route::match(['get', 'post'], 'users/{userId}/addresses/add-address', [AdminController::class, 'add_address'])->name('admin.add_address');


    Route::get('/get-subcategories/{categoryId}', [AdminController::class, 'getSubcategories'])->name('admin.getSubcategories');
    Route::get('/get-sub-subcategories/{categoryId}/{subcategoryId}', [AdminController::class, 'getSubSubcategories'])->name('admin.getSubSubcategories');
    Route::get('/get-services/{categoryId}/{subcategoryId}/{subSubcategoryId}', [AdminController::class, 'getServices'])->name('admin.getServices');
    Route::get('/get-daily-slots', [AdminController::class, 'getDailySlots']);
    
    //Provides Route

    Route::get('providers', [AdminController::class, 'providers'])->name('admin.providers');
    Route::match(['get', 'post'],'providers/add', [AdminController::class, 'add_providers'])->name('admin.add_providers');
    Route::match(['get', 'post'], 'providers/{id}/edit', [AdminController::class, 'edit_providers'])->name('admin.edit_providers');
    Route::delete('providers/{id}/delete', [AdminController::class, 'delete_providers'])->name('admin.delete_providers');

    Route::get('/get-zones/{providerId}', [AdminController::class, 'getZones'])->name('get_zones');
    Route::post('/assign-zones', [AdminController::class, 'assignZones'])->name('admin.assign_zones');


    Route::get('/get-plans/{type}', [AdminController::class, 'getPlans']);
    Route::get('/activate-security/{id}/{planId}', [AdminController::class, 'activateSecurity']);

    // dynamic state & city selection

    Route::get('/get-states/{country_id}', [AdminController::class, 'getStates'])->name('get.states');
    Route::get('/get-cities/{state_id}', [AdminController::class, 'getCities'])->name('get.cities');
    
    // sub_subCategory Route
    Route::get('/sub-sub-categories/{subcategory_id}', [AdminController::class, 'subSubCategory'])->name('admin.subSubCategories');
    Route::match(['get', 'post'], '/admin/add-sub-sub-category/{subcategory_id}', [AdminController::class, 'addSubSubCategory'])->name('admin.addSubSubCategory');
    Route::match(['get', 'post'], 'admin/edit-subsubcategory/{subcategory_id}/{id}', [AdminController::class, 'editSubSubCategory']) ->name('admin.edit_subsubcategory');
    Route::delete('/delete-subsubcategory/{subcategory_id}/{id}', [AdminController::class, 'deleteSubSubCategory'])->name('admin.delete_subsubcategory');
   
    // Service
    Route::get('service/{category_id}/{subcategory_id}/{id}', [AdminController::class, 'service'])->name('admin.service');
    Route::match(['get', 'post'], '/admin/add_service/{category_id}/{subcategory_id}/{sub_subcategory_id}', [AdminController::class, 'addService'])->name('admin.add_service');
    Route::match(['get', 'post'], '/admin/edit_service/{category_id}/{subcategory_id}/{sub_subcategory_id}/{service_id}', [AdminController::class, 'editService'])->name('admin.edit_service');
    Route::delete('/admin/service/{id}', [AdminController::class, 'deleteService'])->name('admin.delete_service');
 

    //transaction
    Route::get('transaction',[AdminController::class,'transaction'])->name('admin.transaction');
    
     //zone
    Route::get('zone', [AdminController::class, 'zone'])->name('admin.zones');
    Route::match(['get', 'post'], 'zone/add', [AdminController::class, 'add_zone'])->name('admin.add_zone');
    // Route::match(['get', 'post'], 'assign_provider', [AdminController::class, 'assign_provider'])->name('admin.assign_provider');
    // Route::get('get_providers/{zone_id}', [AdminController::class, 'get_providers'])->name('admin.get_providers');
    
    Route::get('all-bookings', [AdminController::class, 'all_bookings'])->name('admin.all_bookings');
});


Route::get('terms-and-conditions', [WebsiteController::class, 'terms_conditions']);
Route::get('contact-us', [WebsiteController::class, 'contact_us']);
Route::get('refund-policy', [WebsiteController::class, 'refund_policy']);
Route::get('privacy-policy', [WebsiteController::class, 'privacy_policy']);