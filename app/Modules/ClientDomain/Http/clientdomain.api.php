<?php

namespace App\Modules\ClientDomain\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\ClientDomain\Http\Controllers')->group(function () {
		Route::prefix('clientdomain')->name('clientdomain.')->group(function () {
			//Route::group(['middleware' => ['can:View ClientDomain']], function () {
			Route::post('get', [ClientDomainController::class, 'index'])->name('get')->middleware('permission:view clientdomain');
			Route::post('get-all', [ClientDomainController::class, 'getAll'])->name('get-all')->middleware('permission:view clientdomain');
			Route::post('get/{id}', [ClientDomainController::class, 'show'])->name('show')->middleware('permission:view clientdomain');
			//});

			Route::post('get/domains/{id}', [ClientDomainController::class, 'clientDomains'])->name('clientDomains')->middleware('permission:view clientdomain');

			Route::post('domain/list', [ClientDomainController::class, 'domainList'])->name('domainList')->middleware('permission:view clientdomain');

			Route::post('import', [ClientDomainController::class, 'domainImport'])->name('domainImport')->middleware('permission:import clientdomain');

			Route::get('import/template/download', [ClientDomainController::class, 'domainImportTemplateDownload'])->name('domainImportTemplateDownload')->middleware('permission:view clientdomain');

			//Route::group(['middleware' => ['can:Create ClientDomain']], function () {
			Route::post('store', [ClientDomainController::class, 'store'])->name('store')->middleware('permission:create clientdomain');
			//});

			//Route::group(['middleware' => ['can:Update ClientDomain']], function () {
			Route::post('update/{id}', [ClientDomainController::class, 'update'])->name('update')->middleware('permission:update clientdomain');
			//});

			//Route::group(['middleware' => ['can:Delete ClientDomain']], function () {
			Route::post('delete/{id}', [ClientDomainController::class, 'destroy'])->name('delete')->middleware('permission:delete clientdomain');
			//});
            Route::post('assign-users/{id}', [ClientDomainController::class, 'assignUsersToDomain'])->middleware('permission:assign clientdomain');

            Route::post('rival-backlinks/{id}', [ClientDomainController::class, 'rivalBacklinksClientwise']);
        });
	});
});
