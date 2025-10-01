<?php

use App\Http\Controllers\AnalogController;
use App\Http\Controllers\DigitalController;
use App\Http\Controllers\SuccessController;
use App\Http\Controllers\EmailVerification;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'digital/{hallId}', 'middleware' => 'check-hash'], function() {
    Route::get('/s1/{hash}',                   [DigitalController::class, 'stepOne'])->name('digital.stepOne');
    Route::get('/registration/{hash}',         [DigitalController::class, 'registration'])->name('digital.registration');
    Route::post('/store/{hash}',               [DigitalController::class, 'store'])
        ->withoutMiddleware('check-hash')
        ->name('digital.registration.store');
});

Route::group(['prefix' => '/analog/{hallId}', 'middleware' => 'check-hash'], function() {
    Route::get('/s1/{hash}',                   [AnalogController::class, 'stepOne'])->name('analog.stepOne');
    Route::get('/registration/{hash}',         [AnalogController::class, 'registration'])->name('analog.registration');
    Route::post('/store/{hash}',               [AnalogController::class, 'store'])
        ->withoutMiddleware('check-hash')
        ->name('analog.registration.store');
});


Route::name('success.')->group(function () {
    Route::get('/success/{payid}',              [SuccessController::class, 'index'])->name('index');
    Route::get('/digital/{payid}',              [SuccessController::class, 'digital'])->name('digital');
});

Route::get('/emailverification/{payId}/{email}', [EmailVerification::class, 'emailVerification']);

Route::get('/unsubcribe/{email}', function () {
    return view('unsubscribed');
});

Route::get('/healthcheck', function () {
    return response()->json('status: 200');
});


//Route::any('{any}', function ()                 {return view('errors.404');})->where('any', '.*');
