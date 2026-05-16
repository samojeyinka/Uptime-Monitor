<?php

use App\Http\Controllers\Monitor\CreateMonitorController;
use App\Http\Controllers\Monitor\ListMonitorsController;
use App\Http\Controllers\Monitor\MonitorHistoryController;
use Illuminate\Support\Facades\Route;

Route::post('/monitors', CreateMonitorController::class);
Route::get('/monitors', ListMonitorsController::class);
Route::get('/monitors/{monitor}/history', MonitorHistoryController::class);
