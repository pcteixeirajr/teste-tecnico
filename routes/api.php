<?php

use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\ProcessPayController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function (){
    Route::post('/payments', [PagamentoController::class, 'newPayment']);
    Route::post('/payments/proccess', [ProcessPayController::class, 'proccessPayment']);
    Route::get('/payments', [PagamentoController::class, 'getPayments']);
    Route::get('/payments/{id}', [PagamentoController::class, 'getSinglePayment']);
});