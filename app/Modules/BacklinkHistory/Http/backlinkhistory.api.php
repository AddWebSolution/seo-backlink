<?php

namespace App\Modules\BacklinkHistory\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api','auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\BacklinkHistory\Http\Controllers')->group(function () {
		Route::prefix('backlinkhistory')->name('backlinkhistory.')->group(function () {
			//Route::group(['middleware' => ['can:View BacklinkHistory']], function () {
				Route::post('get', [BacklinkHistoryController::class, 'index'])->name('get');
				Route::post('get-all', [BacklinkHistoryController::class, 'getAll'])->name('get-all');
				Route::post('get/{id}', [BacklinkHistoryController::class, 'show'])->name('show');
			//});

			Route::post('clientdomain/history/compare/{id}', [BacklinkHistoryController::class, 'fetchHistoryData'])->name('fetch-history-data');
			//Route::group(['middleware' => ['can:Create BacklinkHistory']], function () {
				Route::post('store', [BacklinkHistoryController::class, 'store'])->name('store');
				Route::post('updateorcreate', [BacklinkHistoryController::class, 'updateorcreate'])->name('updateorcreate');
			//});

			//Route::group(['middleware' => ['can:Update BacklinkHistory']], function () {
				Route::post('update/{id}', [BacklinkHistoryController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete BacklinkHistory']], function () {
				Route::post('delete/{id}', [BacklinkHistoryController::class, 'destroy'])->name('delete');
			//});
		});
	});
});
