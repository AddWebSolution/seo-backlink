<?php

namespace App\Modules\Client\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\Client\Http\Controllers')->group(function () {
		Route::prefix('client')->name('client.')->group(function () {
			//Route::group(['middleware' => ['can:View Client']], function () {
			Route::post('get', [ClientController::class, 'index'])->name('get');
			Route::post('get-all', [ClientController::class, 'getAll'])->name('get-all');
			Route::post('get/{id}', [ClientController::class, 'show'])->name('show');
			//});

			Route::post('list', [ClientController::class, 'clientList'])->name('clientList');

			Route::post('import', [ClientController::class, 'clientImport'])->name('clientImport');

			Route::get('import/template/download', [ClientController::class, 'clientImportTemplateDownload'])->name('clientImportTemplateDownload');

			//Route::group(['middleware' => ['can:Create Client']], function () {
			Route::post('store', [ClientController::class, 'store'])->name('store');
			//});

			Route::post('upload/profile/pic/{id}', [ClientController::class, 'updateProfilePic']);

			//Route::group(['middleware' => ['can:Update Client']], function () {
			Route::post('update/{id}', [ClientController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete Client']], function () {
			Route::post('delete/{id}', [ClientController::class, 'destroy'])->name('delete');
			//});
		});
	});
});
