<?php

namespace App\Modules\RivalDomain\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api','auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\RivalDomain\Http\Controllers')->group(function () {
		Route::prefix('rivaldomain')->name('rivaldomain.')->group(function () {
			//Route::group(['middleware' => ['can:View RivalDomain']], function () {
				Route::post('get', [RivalDomainController::class, 'index'])->name('get');
				Route::post('get-all', [RivalDomainController::class, 'getAll'])->name('get-all');
				Route::post('get/{id}', [RivalDomainController::class, 'show'])->name('show');
			//});

			Route::post('domain/list', [RivalDomainController::class, 'rivalDomainList'])->name('rivalDomainList');

			Route::post('import', [RivalDomainController::class, 'rivalDomainImport'])->name('rivalDomainImport');

			Route::get('import/template/download', [RivalDomainController::class, 'domainImportTemplateDownload'])->name('domainImportTemplateDownload');

			//Route::group(['middleware' => ['can:Create RivalDomain']], function () {
			Route::post('store', [RivalDomainController::class, 'store'])->name('store');
			//});

			//Route::group(['middleware' => ['can:Update RivalDomain']], function () {
				Route::post('update/{id}', [RivalDomainController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete RivalDomain']], function () {
				Route::post('delete/{id}', [RivalDomainController::class, 'destroy'])->name('delete');
			//});
		});
	});
});
