<?php

namespace App\Modules\User\Http\Controllers;

use Illuminate\Support\Facades\Route;


// =========================
// 🔹 Public Auth Routes
// =========================
Route::prefix('api')->namespace('App\Modules\User\Http\Controllers')->group(function () {
	Route::prefix('auth')->name('auth.')->group(function () {
		Route::post('register', [AuthController::class, 'register'])->name('register');
		Route::post('login', [AuthController::class, 'login'])->name('login');
	});
});


// =========================
// 🔹 Protected User Routes
// =========================
Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\User\Http\Controllers')->group(function () {
		Route::prefix('user')->name('user.')->group(function () {
			//Route::group(['middleware' => ['can:View User']], function () {
			Route::post('get', [UserController::class, 'index'])->name('get');
			Route::post('get-all', [UserController::class, 'getAll'])->name('get-all');
			Route::post('get/{id}', [UserController::class, 'show'])->name('show');
			//});

			//Route::group(['middleware' => ['can:Create User']], function () {
			Route::post('store', [UserController::class, 'store'])->name('store');
			//});

			Route::post('upload/profile/pic', [UserController::class, 'updateProfilePic'])->name('upload_profile_pic');
			//});

			//Route::group(['middleware' => ['can:Update User']], function () {
			Route::post('update/{id}', [UserController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete User']], function () {
			Route::post('delete/{id}', [UserController::class, 'destroy'])->name('delete');
			//});
		});
	});
});
