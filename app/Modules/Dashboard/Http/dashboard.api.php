<?php

namespace App\Modules\Dashboard\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api','auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\Dashboard\Http\Controllers')->group(function () {
		Route::prefix('dashboard')->name('dashboard.')->group(function () {
			//Route::group(['middleware' => ['can:View Dashboard']], function () {
				Route::post('get', [DashboardController::class, 'index'])->name('get');
				Route::post('get-all', [DashboardController::class, 'getAll'])->name('get-all');
				Route::post('get/{id}', [DashboardController::class, 'show'])->name('show');
			//});

			Route::post('stats', [DashboardController::class, 'dashboardStats'])->name('dashboard-stats');

			Route::post('monthly/stats', [DashboardController::class, 'monthlyStats'])->name('monthly-stats');

			//Route::group(['middleware' => ['can:Create Dashboard']], function () {
			Route::post('store', [DashboardController::class, 'store'])->name('store');
			//});

			//Route::group(['middleware' => ['can:Update Dashboard']], function () {
			Route::post('update/{id}', [DashboardController::class, 'update'])->name('update');
			//});

			//Route::group(['middleware' => ['can:Delete Dashboard']], function () {
				Route::post('delete/{id}', [DashboardController::class, 'destroy'])->name('delete');
			//});
		});
	});
});
