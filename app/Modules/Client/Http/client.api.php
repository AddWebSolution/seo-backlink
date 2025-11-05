<?php

namespace App\Modules\Client\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\Client\Http\Controllers')->group(function () {
		Route::prefix('client')->name('client.')->group(function () {
			//Route::group(['middleware' => ['can:View Client']], function () {
			Route::post('get', [ClientController::class, 'index'])->name('get')->middleware('permission:view client');
			Route::post('get-all', [ClientController::class, 'getAll'])->name('get-all')->middleware('permission:view client');
			Route::post('get/{id}', [ClientController::class, 'show'])->name('show')->middleware('permission:view client');
			//});

			Route::post('list', [ClientController::class, 'clientList'])->name('clientList')->middleware('permission:view client');

			Route::post('import', [ClientController::class, 'clientImport'])->name('clientImport')->middleware('permission:import client');

			Route::get('import/template/download', [ClientController::class, 'clientImportTemplateDownload'])->name('clientImportTemplateDownload')->middleware('permission:import client');

			//Route::group(['middleware' => ['can:Create Client']], function () {
			Route::post('store', [ClientController::class, 'store'])->name('store')->middleware('permission:create client');
			//});

			Route::post('upload/profile/pic/{id}', [ClientController::class, 'updateProfilePic'])->middleware('permission:view client');

			//Route::group(['middleware' => ['can:Update Client']], function () {
			Route::post('update/{id}', [ClientController::class, 'update'])->name('update')->middleware('permission:update client');
			//});

			//Route::group(['middleware' => ['can:Delete Client']], function () {
			Route::post('delete/{id}', [ClientController::class, 'destroy'])->name('delete')->middleware('permission:delete client');
			//});
		});
	});
});
