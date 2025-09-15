<?php

namespace App\Modules\KeywordReport\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api','auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\KeywordReport\Http\Controllers')->group(function () {
		Route::prefix('keywordreport')->name('keywordreport.')->group(function () {
			//Route::group(['middleware' => ['can:View KeywordReport']], function () {
				Route::post('get', [KeywordReportController::class, 'index'])->name('get');
				Route::post('get-all', [KeywordReportController::class, 'getAll'])->name('get-all');
				Route::post('get/{id}', [KeywordReportController::class, 'show'])->name('show');
			//});

			//Route::group(['middleware' => ['can:Create KeywordReport']], function () {
				Route::post('store', [KeywordReportController::class, 'store'])->name('store');
			//});

			//Route::group(['middleware' => ['can:Update KeywordReport']], function () {
				Route::post('update/{id}', [KeywordReportController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete KeywordReport']], function () {
				Route::post('delete/{id}', [KeywordReportController::class, 'destroy'])->name('delete');
			//});
		});
	});
});
