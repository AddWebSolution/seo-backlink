<?php

namespace App\Modules\Backlinkreport\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\Backlinkreport\Http\Controllers')->group(function () {
		Route::prefix('backlinkreport')->name('backlinkreport.')->group(function () {
			//Route::group(['middleware' => ['can:View Backlinkreport']], function () {
			Route::post('get', [BacklinkreportController::class, 'index'])->name('get');
			Route::post('get-all', [BacklinkreportController::class, 'getAll'])->name('get-all');
			Route::post('get/{id}', [BacklinkreportController::class, 'show'])->name('show');
			//});

			//Route::group(['middleware' => ['can:Create Backlinkreport']], function () {
			Route::post('store', [BacklinkreportController::class, 'store'])->name('store');
			//});

			//Route::group(['middleware' => ['can:Update Backlinkreport']], function () {
			Route::post('update/{id}', [BacklinkreportController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete Backlinkreport']], function () {
			Route::post('delete/{id}', [BacklinkreportController::class, 'destroy'])->name('delete');
			//});
		});
	});
});
