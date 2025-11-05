<?php

namespace App\Modules\RivalDomain\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api','auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\RivalDomain\Http\Controllers')->group(function () {
		Route::prefix('rivaldomain')->name('rivaldomain.')->group(function () {
			//Route::group(['middleware' => ['can:View RivalDomain']], function () {
				Route::post('get', [RivalDomainController::class, 'index'])->name('get')->middleware('permission:view rivaldomain');
				Route::post('get-all', [RivalDomainController::class, 'getAll'])->name('get-all')->middleware('permission:view rivaldomain');
				Route::post('get/{id}', [RivalDomainController::class, 'show'])->name('show')->middleware('permission:view rivaldomain');
			//});

			Route::post('get/domains/{id}', [RivalDomainController::class, 'rivalDomains'])->name('rivalDomains')->middleware('permission:view rivaldomain');

			Route::post('domain/list', [RivalDomainController::class, 'rivalDomainList'])->name('rivalDomainList')->middleware('permission:view rivaldomain');

			Route::post('import', [RivalDomainController::class, 'rivalDomainImport'])->name('rivalDomainImport')->middleware('permission:import rivaldomain');

			Route::get('import/template/download', [RivalDomainController::class, 'domainImportTemplateDownload'])->name('domainImportTemplateDownload')->middleware('permission:view rivaldomain');

			//Route::group(['middleware' => ['can:Create RivalDomain']], function () {
			Route::post('store', [RivalDomainController::class, 'store'])->name('store')->middleware('permission:create rivaldomain');
			//});

			//Route::group(['middleware' => ['can:Update RivalDomain']], function () {
				Route::post('update/{id}', [RivalDomainController::class, 'update'])->name('update')->middleware('permission:update rivaldomain');
			//});

			//Route::group(['middleware' => ['can:Delete RivalDomain']], function () {
				Route::post('delete/{id}', [RivalDomainController::class, 'destroy'])->name('delete')->middleware('permission:delete rivaldomain');
			//});
		});
	});
});
