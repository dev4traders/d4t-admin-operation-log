<?php

use Illuminate\Support\Facades\Route;
use Dcat\Admin\OperationLog\OperationLogServiceProvider;
use Dcat\Admin\OperationLog\Http\Controllers\LogController;

Route::get(OperationLogServiceProvider::URL_OP_LOG, LogController::class.'@index')->name('dcat-admin.operation-log.index');
Route::delete(OperationLogServiceProvider::URL_OP_LOG.'/{id}', LogController::class.'@destroy')->name('dcat-admin.operation-log.destroy');