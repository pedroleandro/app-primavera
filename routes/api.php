<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('/v1')->group(function () {

    // Rotas de autenticação
    Route::prefix('/auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);

        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/user', function (Request $request) {
                return $request->user();
            });
        });
    });


    // Rotas do dashboard
    Route::middleware('auth:api')->group(function () {

        Route::prefix('/dashboard')->group(function () {
            Route::get('/filiais', [DashboardController::class, 'getFiliais']);
            Route::get('/faturamento', [DashboardController::class, 'getFaturamento']);
            Route::get('/faturamento-mes-anterior', [DashboardController::class, 'getFaturamentoMesAnterior']);
            Route::get('/pedidos', [DashboardController::class, 'getPedidos']);
            Route::get('/pedidos-mes-anterior', [DashboardController::class, 'getPedidosMesAnterior']);
            Route::get('/ultimos-pedidos', [DashboardController::class, 'getUltimosPedidos']);
            Route::get('/ranking-clientes', [DashboardController::class, 'rankingClientes']);
            Route::get('/ranking-clientes-completo', [DashboardController::class, 'rankingCompletoClientes']);
            Route::get('/ranking-produtos', [DashboardController::class, 'rankingProdutos']);
            Route::get('/ranking-produtos-completo', [DashboardController::class, 'rankingCompletoProdutos']);
        });

    });

});

