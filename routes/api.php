<?php

use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\CashController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


//Auth::loginUsingId(1);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('me', [MeController::class, '__invoke']);

    Route::prefix("cash")->group(function() {
        Route::post('', [CashController::class, 'store']);
        Route::get('', [CashController::class, 'index']);
        Route::get('{cash:slug}', [CashController::class, 'show']);
    });
});
