<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/dashboard/filiais', [DashboardController::class, 'getFiliais']);
Route::get('/dashboard/faturamento', [DashboardController::class, 'getFaturamento']);
Route::get('/dashboard/faturamento-mes-anterior', [DashboardController::class, 'getFaturamentoMesAnterior']);
Route::get('/dashboard/pedidos', [DashboardController::class, 'getPedidos']);
Route::get('/dashboard/pedidos-mes-anterior', [DashboardController::class, 'getPedidosMesAnterior']);
Route::get('/dashboard/ultimos-pedidos', [DashboardController::class, 'getUltimosPedidos']);
Route::get('/dashboard/ranking-clientes', [DashboardController::class, 'rankingClientes']);
Route::get('/dashboard/ranking-clientes-completo', [DashboardController::class, 'rankingCompletoClientes']);
Route::get('/dashboard/ranking-produtos', [DashboardController::class, 'rankingProdutos']);
Route::get('/dashboard/ranking-produtos-completo', [DashboardController::class, 'rankingCompletoProdutos']);
