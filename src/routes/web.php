<?php

use App\Http\Controllers\OAuth\CallbackFromProviderController;
use App\Http\Controllers\OAuth\RedirectToProviderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oauth/{provider}/redirect', RedirectToProviderController::class)
    ->name('oauth.redirect');
Route::get('/oauth/{provider}-callback', CallbackFromProviderController::class)
    ->name('oauth.callback');
