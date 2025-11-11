<?php

namespace App\Modules\Keyword\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\Keyword\Http\Controllers')->group(function () {
		Route::prefix('keyword')->name('keyword.')->group(function () {
			//Route::group(['middleware' => ['can:View Keyword']], function () {
			Route::post('get', [KeywordController::class, 'index'])->name('get')->middleware('permission:view keyword');
			Route::post('get-all', [KeywordController::class, 'getAll'])->name('get-all')->middleware('permission:view keyword');
			Route::post('get/{id}', [KeywordController::class, 'show'])->name('show')->middleware('permission:view keyword');
			//});

			//Route::group(['middleware' => ['can:Create Keyword']], function () {
			Route::post('store', [KeywordController::class, 'store'])->name('store')->middleware('permission:create keyword');
			//});


			Route::post('list', [KeywordController::class, 'keywordList'])->name('keywordList')->middleware('permission:view keyword');


			Route::get('import/template/download', [KeywordController::class, 'keywordImportTemplateDownload'])->middleware('permission:import keyword');

			Route::post('import', [KeywordController::class, 'keywordImport'])->middleware('permission:import keyword');

			Route::get('get/keyword/data/history/{id}', [KeywordController::class, 'keywordHistory'])->middleware('permission:view keywordreport');

			//Route::group(['middleware' => ['can:Update Keyword']], function () {
			Route::post('update/{id}', [KeywordController::class, 'update'])->name('update')->middleware('permission:update keyword');
			//});

			//Route::group(['middleware' => ['can:Delete Keyword']], function () {
			Route::post('delete/{id}', [KeywordController::class, 'destroy'])->name('delete')->middleware('permission:delete keyword');
			//});
		});
	});
});
