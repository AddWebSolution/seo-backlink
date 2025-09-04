<?php

namespace App\Modules\ClientDomain\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\ClientDomain\Http\Controllers')->group(function () {
		Route::prefix('clientdomain')->name('clientdomain.')->group(function () {
			//Route::group(['middleware' => ['can:View ClientDomain']], function () {
			Route::post('get', [ClientDomainController::class, 'index'])->name('get');
			Route::post('get-all', [ClientDomainController::class, 'getAll'])->name('get-all');
			Route::post('get/{id}', [ClientDomainController::class, 'show'])->name('show');
			//});

			//Route::group(['middleware' => ['can:Create ClientDomain']], function () {
			Route::post('store', [ClientDomainController::class, 'store'])->name('store');
			//});

			//Route::group(['middleware' => ['can:Update ClientDomain']], function () {
			Route::post('update/{id}', [ClientDomainController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete ClientDomain']], function () {
			Route::post('delete/{id}', [ClientDomainController::class, 'destroy'])->name('delete');
			//});
		});
	});
});
