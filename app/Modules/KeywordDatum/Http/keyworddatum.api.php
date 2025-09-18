<?php

namespace App\Modules\KeywordDatum\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api','auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\KeywordDatum\Http\Controllers')->group(function () {
		Route::prefix('keyworddatum')->name('keyworddatum.')->group(function () {
			//Route::group(['middleware' => ['can:View KeywordDatum']], function () {
				Route::post('get', [KeywordDatumController::class, 'index'])->name('get');
				Route::post('get-all', [KeywordDatumController::class, 'getAll'])->name('get-all');
				Route::post('get/{id}', [KeywordDatumController::class, 'show'])->name('show');
			//});

			//Route::group(['middleware' => ['can:Create KeywordDatum']], function () {
				Route::post('store', [KeywordDatumController::class, 'store'])->name('store');
			//});

			//Route::group(['middleware' => ['can:Update KeywordDatum']], function () {
				Route::post('update/{id}', [KeywordDatumController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete KeywordDatum']], function () {
				Route::post('delete/{id}', [KeywordDatumController::class, 'destroy'])->name('delete');
			//});
		});
	});
});
