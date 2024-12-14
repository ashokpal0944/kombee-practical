<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['namespace' => 'App\Http\Controllers\Auth'], function () 
{
	Route::post('user-login', 'LoginController@userLogin')->name('user-login');	
	Route::post('user-register', 'RegisterController@userRegister')->name('user-register');	
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//User Module
Route::get('/user-list', [App\Http\Controllers\UserController::class, 'userList'])->name('user-list');
Route::post('/get-user-list-ajax', [App\Http\Controllers\UserController::class, 'userListAjax']);
Route::get('/user-edit/{id}', [App\Http\Controllers\UserController::class, 'userEdit'])->name('user-edit');
Route::post('/user-edit/{id}', [App\Http\Controllers\UserController::class, 'userUpdate']);
Route::post('/user-delete/{id}', [App\Http\Controllers\UserController::class, 'userDelete']);

Route::get('export-users/{format}', [App\Http\Controllers\UserController::class, 'exportUsers']);

//Role Module
Route::get('/role-list', [App\Http\Controllers\RoleController::class, 'roleList'])->name('role-list');
Route::post('/get-role-list-ajax', [App\Http\Controllers\RoleController::class, 'roleListAjax']);
Route::get('/role-add', [App\Http\Controllers\RoleController::class, 'roleAdd'])->name('role-add');
Route::post('/role-add', [App\Http\Controllers\RoleController::class, 'roleInsert']);
Route::get('/role-edit/{id}', [App\Http\Controllers\RoleController::class, 'roleEdit'])->name('role-edit');
Route::post('/role-edit/{id}', [App\Http\Controllers\RoleController::class, 'roleUpdate']);
Route::post('/role-delete/{id}', [App\Http\Controllers\RoleController::class, 'roleDelete']);


//Supplier List
Route::get('/supplier-list', [App\Http\Controllers\SupplierController::class, 'supplierList'])->name('supplier-list')->middleware(['auth', 'role:Admin|Manager', 'permission:view_supplier']);
Route::post('/get-supplier-list-ajax', [App\Http\Controllers\SupplierController::class, 'supplierListAjax']);
Route::get('/supplier-add', [App\Http\Controllers\SupplierController::class, 'supplierAdd'])->name('supplier-add')->middleware(['auth', 'role:Admin|Manager',  'permission:create_supplier']);
Route::post('/supplier-add', [App\Http\Controllers\SupplierController::class, 'supplierInsert']);
Route::get('/supplier-edit/{id}', [App\Http\Controllers\SupplierController::class, 'supplierEdit'])->name('supplier-edit')->middleware(['auth', 'role:Admin|Manager',  'permission:edit_supplier']);
Route::post('/supplier-edit/{id}', [App\Http\Controllers\SupplierController::class, 'supplierUpdate'])->middleware(['auth', 'role:Admin|Manager', 'permission:edit_supplier']);
Route::post('/supplier-delete/{id}', [App\Http\Controllers\SupplierController::class, 'supplierDelete'])->middleware([ 'auth', 'role:Admin|Manager',  'permission:delete_supplier']);

//Customers Module
Route::get('/customers-list', [App\Http\Controllers\CustomerController::class, 'customersList'])->name('customers-list')->middleware(['auth', 'role:Admin|Manager','permission:view_customer']);
Route::post('/get-customers-list-ajax', [App\Http\Controllers\CustomerController::class, 'customersListAjax']);
Route::get('/customers-add', [App\Http\Controllers\CustomerController::class, 'customersAdd'])->name('customers-add')->middleware(['auth', 'role:Admin|Manager','permission:create_customer']);
Route::post('/customers-add', [App\Http\Controllers\CustomerController::class, 'customersInsert']);
Route::get('/customers-edit/{id}', [App\Http\Controllers\CustomerController::class, 'customersEdit'])->name('customers-edit')->middleware(['auth', 'role:Admin|Manager', 'permission:edit_customer']);
Route::post('/customers-edit/{id}', [App\Http\Controllers\CustomerController::class, 'customersUpdate'])->middleware(['auth', 'role:Admin|Manager','permission:edit_customer']);
Route::post('/customers-delete/{id}', [App\Http\Controllers\CustomerController::class, 'customersDelete'])->middleware(['auth', 'role:Admin|Manager','permission:delete_customer']);

Route::group(['namespace' => 'App\Http\Controllers'], function () 
{
	Route::post('get-state-data', 'CommonController@getStateData')->name('get-state-data');	
	Route::post('get-city-data', 'CommonController@getCityData')->name('get-city-data');
	
	Route::post('check-email', 'CommonController@checkEmail')->name('check-email');	
	Route::post('check-contact', 'CommonController@checkContact')->name('check-contact');	
});
