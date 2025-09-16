<?php

namespace App\Modules\BacklinkDatum\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api','auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\BacklinkDatum\Http\Controllers')->group(function () {
		Route::prefix('backlinkdatum')->name('backlinkdatum.')->group(function () {
			//Route::group(['middleware' => ['can:View BacklinkDatum']], function () {
				Route::post('get', [BacklinkDatumController::class, 'index'])->name('get');
				Route::post('get-all', [BacklinkDatumController::class, 'getAll'])->name('get-all');
				Route::post('get/{id}', [BacklinkDatumController::class, 'show'])->name('show');
			//});

			//Route::group(['middleware' => ['can:Create BacklinkDatum']], function () {
				Route::post('store', [BacklinkDatumController::class, 'store'])->name('store');
			//});

			//Route::group(['middleware' => ['can:Update BacklinkDatum']], function () {
				Route::post('update/{id}', [BacklinkDatumController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete BacklinkDatum']], function () {
				Route::post('delete/{id}', [BacklinkDatumController::class, 'destroy'])->name('delete');
			//});
		});
	});
});
