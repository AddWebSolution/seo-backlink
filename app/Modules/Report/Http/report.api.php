<?php

namespace App\Modules\Report\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Modules\Report\Http\Controllers\ReportController;

Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
	Route::prefix('api')->namespace('App\Modules\Report\Http\Controllers')->group(function () {
		Route::prefix('report')->name('report.')->group(function () {

			//Route::group(['middleware' => ['can:View Report']], function () {
			Route::post('get', [ReportController::class, 'index'])->name('get')->middleware('permission:view report');
			Route::post('get-all', [ReportController::class, 'getAll'])->name('get-all')->middleware('permission:view report');
			Route::post('get/{id}', [ReportController::class, 'show'])->name('show')->middleware('permission:view report');
			//});

			Route::post('export', [ReportController::class, 'reportExport'])->middleware('permission:export report');

			Route::post('backlinks/{id}', [ReportController::class, 'backlinkshow'])->middleware('permission:view backlinkdatum');

			//Route::group(['middleware' => ['can:Create Report']], function () {
			Route::post('store', [ReportController::class, 'store'])->name('store')->middleware('permission:create report');
			//});

			//Route::group(['middleware' => ['can:Update Report']], function () {
			Route::post('update/{id}', [ReportController::class, 'update'])->name('update')->middleware('permission:edit report');
			//});

			Route::post('delete/{id}', [ReportController::class, 'destroy'])->name('delete')->middleware('permission:delete report');
		});
	});
});
