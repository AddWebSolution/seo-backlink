<?php

namespace App\Modules\Report\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Modules\Report\Http\Controllers\ReportController;

Route::group(['middleware' => ['api', 'auth:passport']], function () {
	Route::prefix('api')->namespace('App\Modules\Report\Http\Controllers')->group(function () {
		Route::prefix('report')->name('report.')->group(function () {
			//Route::group(['middleware' => ['can:View Report']], function () {
			Route::post('get', [ReportController::class, 'index'])->name('get');
			Route::post('get-all', [ReportController::class, 'getAll'])->name('get-all');
			Route::post('get/{id}', [ReportController::class, 'show'])->name('show');
			//});

			Route::post('export', [ReportController::class, 'reportExport']);

			Route::post('backlinks/{id}', [ReportController::class, 'backlinkshow']);

			//Route::group(['middleware' => ['can:Create Report']], function () {
			Route::post('store', [ReportController::class, 'store'])->name('store');
			//});

			//Route::group(['middleware' => ['can:Update Report']], function () {
			Route::post('update/{id}', [ReportController::class, 'update'])->name('update');
			//});

			Route::group(['middleware' => ['can:Delete Report']], function () {
			Route::post('delete/{id}', [ReportController::class, 'destroy'])->name('delete');
			});
		});
	});
});
