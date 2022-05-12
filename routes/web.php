<?php

use App\Http\Controllers\TestController;
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

Route::get('/test/{id}', [TestController::class, 'index']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Users
Route::post('users/getUserList', [\App\Http\Controllers\User\UserController::class, 'getUserList']);
Route::resource('users', 'App\Http\Controllers\User\UserController');
// ACL Role
Route::post('access-control/roles/getSelect2Option', [\App\Http\Controllers\User\AccessControl\RoleController::class, 'getSelect2Option']);
Route::post('access-control/roles/getRoleList', [\App\Http\Controllers\User\AccessControl\RoleController::class, 'getRoleList']);
Route::resource('access-control/roles', \App\Http\Controllers\User\AccessControl\RoleController::class);
// ACL Permissions
Route::post('access-control/permissions/getSelect2Option', [\App\Http\Controllers\User\AccessControl\PermissionController::class, 'getSelect2Option']);
Route::post('access-control/permissions/getPermissionList', [\App\Http\Controllers\User\AccessControl\PermissionController::class, 'getPermissionList']);
Route::resource('access-control/permissions', \App\Http\Controllers\User\AccessControl\PermissionController::class);
