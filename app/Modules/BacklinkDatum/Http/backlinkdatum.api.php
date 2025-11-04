<?php

namespace App\Modules\BacklinkDatum\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api','auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\BacklinkDatum\Http\Controllers')->group(function () {
		Route::prefix('backlinkdatum')->name('backlinkdatum.')->group(function () {
			//Route::group(['middleware' => ['can:View BacklinkDatum']], function () {
				Route::post('get', [BacklinkDatumController::class, 'index'])->name('get')->middleware('permission:view backlinkdatum');
				Route::post('get-all', [BacklinkDatumController::class, 'getAll'])->name('get-all')->middleware('permission:view backlinkdatum');
				Route::post('get/{id}', [BacklinkDatumController::class, 'show'])->name('show')->middleware('permission:view backlinkdatum');
			//});

			//Route::group(['middleware' => ['can:Create BacklinkDatum']], function () {
				Route::post('store', [BacklinkDatumController::class, 'store'])->name('store')->middleware('permission:create backlinkdatum');
			//});

			//Route::group(['middleware' => ['can:Update BacklinkDatum']], function () {
				Route::post('update/{id}', [BacklinkDatumController::class, 'update'])->name('update')->middleware('permission:update backlinkdatum');
			//});

			//Route::group(['middleware' => ['can:Delete BacklinkDatum']], function () {
				Route::post('delete/{id}', [BacklinkDatumController::class, 'destroy'])->name('delete')->middleware('permission:delete backlinkdatum');
			//});
		});
	});
});
