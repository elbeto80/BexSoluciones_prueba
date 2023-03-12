<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true
    ]);
});


Route::group(['prefix' => 'v1'], function () {
    Route::post('/register', [\App\Http\Controllers\UserController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\UserController::class, 'login']);

    Route::group(['middleware' => ['jwt.verify']], function () {
        Route::post('/expire',  [\App\Http\Controllers\UserController::class, 'expireToken']);

        Route::get('/users',  [\App\Http\Controllers\UserController::class, 'getAllUsers']);
        Route::get('/users/{id}',  [\App\Http\Controllers\UserController::class, 'getUser']);
        Route::put('/users/update',  [\App\Http\Controllers\UserController::class, 'updateUser']);
        Route::delete('/users/delete/{id}',  [\App\Http\Controllers\UserController::class, 'deleteUser']);

        Route::apiResources([
            'products' => \App\Http\Controllers\ProductController::class,
        ]);
    });
});
