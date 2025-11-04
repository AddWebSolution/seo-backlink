<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Client\Http\Controllers\ClientController;
use App\Modules\User\Http\Controllers\AuthController;
use App\Modules\User\Http\Controllers\UserController;
use App\Modules\User\Http\Controllers\RoleController;


// =============================
// 🔓 Public Auth Routes
// =============================
Route::prefix('api/auth')->name('auth.')->group(function () {
	Route::post('register', [AuthController::class, 'register'])->name('register');
	Route::post('register/user', [AuthController::class, 'userRegister'])->name('register.user');
	Route::post('login', [AuthController::class, 'login'])->name('login');
});


// =============================
// 🔐 Protected Routes
// =============================
Route::prefix('api')
	->middleware(['api', 'auth:sanctum'])
	->group(function () {

		// =========================
		// 👤 User Management
		// =========================

		Route::prefix('user')->name('user.')->group(function () {

			Route::post('get', [UserController::class, 'index'])->name('get')->middleware('permission:view user');
			
			Route::post('get-all', [UserController::class, 'getAll'])->name('get-all')->middleware('permission:view user');

			Route::post('get/{id}', [UserController::class, 'show'])->name('show')->middleware('permission:view user');

			Route::post('store', [UserController::class, 'store'])->name('store')->middleware('permission:create user');

			Route::post('update/{id}', [UserController::class, 'update'])->name('update')->middleware('permission:update user');
			
			Route::post('import', [ClientController::class, 'clientImport'])->name('clientImport')->middleware('permission:import user');

			Route::get('import/template/download', [ClientController::class, 'clientImportTemplateDownload'])->name('clientImportTemplateDownload');


			Route::post('delete/{id}', [UserController::class, 'destroy'])->name('delete')->middleware('permission:delete user');

			Route::post('/profile/upload', [UserController::class, 'updateProfilePic'])->name('profile.upload');
		});


		// =========================
		// 🧩 Client 
		// =========================

		Route::prefix('client')->name('client.')->group(function () {

			Route::post('get', [UserController::class, 'index'])->name('get')->middleware('permission:view client');

			Route::post('get-all', [UserController::class, 'getAll'])->name('get-all')->middleware('permission:view client');

			Route::post('get/{id}', [UserController::class, 'show'])->name('show')->middleware('permission:view client');

			Route::post('store', [UserController::class, 'store'])->name('store')->middleware('permission:create client');

			Route::post('domains', [UserController::class, 'clientDomains'])->name('clientDomains')->middleware('permission:create client');

			Route::post('name/list', [UserController::class, 'clientList'])->middleware('permission:create client');

			Route::post('import', [ClientController::class, 'clientImport'])->name('clientImport')->middleware('permission:import client');

			Route::get('import/template/download', [ClientController::class, 'clientImportTemplateDownload'])->name('clientImportTemplateDownload')->middleware('permission:import client');

			Route::post('update/{id}', [UserController::class, 'update'])->name('update')->middleware('permission:update client');

			Route::post('delete/{id}', [UserController::class, 'destroy'])->name('delete')->middleware('permission:delete client');
		});


		// =========================
		// 🧩 Roles & Permissions
		// =========================
		Route::prefix('roles')->name('roles.')->group(function () {

			Route::get('/', [RoleController::class, 'roleList'])->name('index')->middleware('permission:view role_permission');
			Route::post('/create', [RoleController::class, 'store'])->name('store')->middleware('permission:create role_permission');
			Route::post('/edit/{roleId}', [RoleController::class, 'update'])->name('update')->middleware('permission:update role_permission');

			Route::get('/{roleId}/permissions', [RoleController::class, 'rolePermission'])->name('permissions.view')->middleware('permission:create role_permission');
			Route::get('/permissions/all', [RoleController::class, 'permissionsList'])->name('permissions.list')->middleware('permission:create role_permission');
		});
	});
