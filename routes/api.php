<?php

use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [UserController::class, 'login']);

Route::post('/payments', [PagamentoController::class, 'newPayment']);
Route::get('/payments', [PagamentoController::class, 'getPayments']);
Route::get('/payments/{id}', [PagamentoController::class, 'getSinglePayment']);
