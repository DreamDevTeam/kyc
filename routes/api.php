<?php

use App\Http\Controllers\AnalogController;
use App\Http\Controllers\DigitalController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'digital'], function() {
    Route::get('{type}/{tid}',          [DigitalController::class, 'getImg'])->where('type', '^(idcard|idback|selfie)$');
});

Route::post('getPhotos',                [AnalogController::class, 'getPhotos'])->middleware('check-secret');
Route::post('getText',                  [AnalogController::class, 'getText'])->middleware('check-secret');
Route::group(['prefix' => 'analog'], function() {
    Route::post('s1/store',             [AnalogController::class, 'stepOneStore'])->name('analog.stepOneStore');
});



