<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\SubscriptionController;

Route::get('/weather', [WeatherController::class, 'getWeather']);
Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
Route::get('/confirm/{token}', [SubscriptionController::class, 'confirm']);
Route::post('/get-token', [SubscriptionController::class, 'getTokenByEmail']);
Route::get('/unsubscribe/{token}', [SubscriptionController::class, 'unsubscribe']);

