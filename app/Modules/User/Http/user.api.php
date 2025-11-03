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

			Route::post('get', [UserController::class, 'index'])->name('get');
			Route::post('get-all', [UserController::class, 'getAll'])->name('get-all');
			Route::post('get/{id}', [UserController::class, 'show'])->name('show');

			//Route::group(['middleware' => ['can:Create KeywordReport']], function () {
			Route::post('store', [UserController::class, 'store'])->name('store');
			//});

			//Route::group(['middleware' => ['can:Update KeywordReport']], function () {
			Route::post('update/{id}', [UserController::class, 'update'])->name('update');
			//});

			Route::post('import', [ClientController::class, 'clientImport'])->name('clientImport');

			Route::get('import/template/download', [ClientController::class, 'clientImportTemplateDownload'])->name('clientImportTemplateDownload');


			//Route::group(['middleware' => ['can:Delete KeywordReport']], function () {
			Route::post('delete/{id}', [UserController::class, 'destroy'])->name('delete');
			//});

			Route::post('/profile/upload', [UserController::class, 'updateProfilePic'])->name('profile.upload');
		});


		// =========================
		// 🧩 Client 
		// =========================

		Route::prefix('client')->name('client.')->group(function () {

			Route::post('get', [UserController::class, 'index'])->name('get');
			Route::post('get-all', [UserController::class, 'getAll'])->name('get-all');
			Route::post('get/{id}', [UserController::class, 'show'])->name('show');

			//Route::group(['middleware' => ['can:Create KeywordReport']], function () {
			Route::post('store', [UserController::class, 'store'])->name('store');
			//});

			Route::post('name/list', [UserController::class, 'clientList']);

			Route::post('import', [ClientController::class, 'clientImport'])->name('clientImport');

			Route::get('import/template/download', [ClientController::class, 'clientImportTemplateDownload'])->name('clientImportTemplateDownload');

			//Route::group(['middleware' => ['can:Update KeywordReport']], function () {
			Route::post('update/{id}', [UserController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete KeywordReport']], function () {
			Route::post('delete/{id}', [UserController::class, 'destroy'])->name('delete');
		});


		// =========================
		// 🧩 Roles & Permissions
		// =========================
		Route::prefix('roles')->name('roles.')->group(function () {

			Route::get('/', [RoleController::class, 'roleList'])->name('index');
			Route::post('/create', [RoleController::class, 'store'])->name('store');
			Route::post('/edit/{roleId}', [RoleController::class, 'update'])->name('update');

			Route::get('/{roleId}/permissions', [RoleController::class, 'rolePermission'])->name('permissions.view');
			Route::get('/permissions/all', [RoleController::class, 'permissionsList'])->name('permissions.list');
		});
	});
