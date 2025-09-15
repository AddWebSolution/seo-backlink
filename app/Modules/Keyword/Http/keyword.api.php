<?php

namespace App\Modules\Keyword\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api','auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\Keyword\Http\Controllers')->group(function () {
		Route::prefix('keyword')->name('keyword.')->group(function () {
			//Route::group(['middleware' => ['can:View Keyword']], function () {
				Route::post('get', [KeywordController::class, 'index'])->name('get');
				Route::post('get-all', [KeywordController::class, 'getAll'])->name('get-all');
				Route::post('get/{id}', [KeywordController::class, 'show'])->name('show');
			//});

			//Route::group(['middleware' => ['can:Create Keyword']], function () {
				Route::post('store', [KeywordController::class, 'store'])->name('store');
			//});

			Route::get('import/template/download', [KeywordController::class, 'keywordImportTemplateDownload']);

			Route::post('import', [KeywordController::class, 'keywordImport']);


			//Route::group(['middleware' => ['can:Update Keyword']], function () {
				Route::post('update/{id}', [KeywordController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete Keyword']], function () {
				Route::post('delete/{id}', [KeywordController::class, 'destroy'])->name('delete');
			//});
		});
	});
});
