<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarroController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LocacaoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('carros', CarroController::class);
    Route::apiResource('locacoes', LocacaoController::class);
    Route::apiResource('marcas', MarcaController::class);
    Route::apiResource('modelos', ModeloController::class);
});


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::post('me', [AuthController::class, 'me']);
