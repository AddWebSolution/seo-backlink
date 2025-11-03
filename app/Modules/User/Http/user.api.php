<?php

use Illuminate\Support\Facades\Route;
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
		Route::prefix('users')->name('users.')->group(function () {

			Route::post('/', [UserController::class, 'index'])->name('index'); // list with filters/pagination
			Route::get('/all', [UserController::class, 'getAll'])->name('all'); // all users
			Route::get('/{id}', [UserController::class, 'show'])->name('show');

			Route::post('/', [UserController::class, 'store'])->name('store');
			Route::put('/{id}', [UserController::class, 'update'])->name('update');
			Route::delete('/{id}', [UserController::class, 'destroy'])->name('delete');

			Route::post('/profile/upload', [UserController::class, 'updateProfilePic'])->name('profile.upload');
		});

		
		// =========================
		// 🧩 Client 
		// =========================

		Route::prefix('client')->name('client.')->group(function () {

			Route::get('/names', [UserController::class, 'clientList'])->name('clients.names');
			Route::get('/list', [UserController::class, 'clientUserList'])->name('clients.users');
			Route::get('/clients/all', [UserController::class, 'ClientListByRole'])->name('clients.all');

			Route::get('/{clientId}/domains', [UserController::class, 'clientDomains'])->name('clients.domains');

			Route::post('/profile/upload', [UserController::class, 'updateProfilePic'])->name('profile.upload');
		});


		// =========================
		// 🧩 Roles & Permissions
		// =========================
		Route::prefix('roles')->name('roles.')->group(function () {

			Route::get('/', [RoleController::class, 'roleList'])->name('index');
			Route::post('/', [RoleController::class, 'store'])->name('store');
			Route::put('/{roleId}', [RoleController::class, 'update'])->name('update');

			Route::get('/{roleId}/permissions', [RoleController::class, 'rolePermission'])->name('permissions.view');
			Route::get('/permissions/all', [RoleController::class, 'permissionsList'])->name('permissions.list');
		});
	});
