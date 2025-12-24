<?php

use App\Http\Controllers\CityController;
use App\Http\Middleware\ApiAuth;
use App\Http\Middleware\LogApiRequests;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api', LogApiRequests::class, ApiAuth::class])->group(function () {
    Route::apiResource('cities', CityController::class);
});
