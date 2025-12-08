<?php

namespace App\Modules\MasterBacklink\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api','auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\MasterBacklink\Http\Controllers')->group(function () {
		Route::prefix('masterbacklink')->name('masterbacklink.')->group(function () {
			//Route::group(['middleware' => ['can:View MasterBacklink']], function () {
				Route::post('get', [MasterBacklinkController::class, 'index'])->name('get');
				Route::post('get-all', [MasterBacklinkController::class, 'getAll'])->name('get-all');
				Route::post('get/{id}', [MasterBacklinkController::class, 'show'])->name('show');
			//});

			//Route::group(['middleware' => ['can:Create MasterBacklink']], function () {
				Route::post('store', [MasterBacklinkController::class, 'store'])->name('store');
			//});

			//Route::group(['middleware' => ['can:Update MasterBacklink']], function () {
				Route::post('update/{id}', [MasterBacklinkController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete MasterBacklink']], function () {
				Route::post('delete/{id}', [MasterBacklinkController::class, 'destroy'])->name('delete');
			//});

            Route::post('import', [MasterBacklinkController::class, 'importMasterBacklinks']);

            Route::get('import/template/download', [MasterBacklinkController::class, 'masterBacklinksImportTemplateDownload'])->name('masterBacklinksImportTemplateDownload');
		});
	});
});
