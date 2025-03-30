<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function getFiliais()
    {
        $filiais = DB::table('faturamento')
            ->select('codigoFilial', 'filial')
            ->distinct()
            ->orderBy('codigoFilial')
            ->get();

        return response()->json($filiais);
    }

    public function getFaturamento(Request $request)
    {
        $anoAtual = Carbon::now()->year;
        $mesAtual = Carbon::now()->month;

        $filial = $request->input('filial');

        $query = DB::table('vendas_clientes');

        if (!empty($filial) && $filial !== 'Todas') {
            $query->where('filial', $filial);
        }

        $faturamentoAnual = $query->whereYear('dataPedidoFaturado', $anoAtual)
            ->sum('valorFaturadoPedido');

        $faturamentoAtual = $query->whereYear('dataPedidoFaturado', $anoAtual)
            ->whereMonth('dataPedidoFaturado', $mesAtual)
            ->sum('valorFaturadoPedido');

        return response()->json([
            'faturamentoAnual' => $faturamentoAnual,
            'faturamentoAtual' => $faturamentoAtual
        ]);
    }

    public function getFaturamentoMesAnterior(Request $request)
    {
        $anoAtual = Carbon::now()->year;
        $mesAtual = Carbon::now()->month;

        $mesAnterior = $mesAtual === 1 ? 12 : $mesAtual - 1;
        $anoAnterior = $mesAnterior === 12 ? $anoAtual - 1 : $anoAtual;

        $filial = $request->input('filial');

        $query = DB::table('vendas_clientes');

        if (!empty($filial) && $filial !== 'Todas') {
            $query->where('filial', $filial);
        }

        $faturamentoMesAnterior = $query->whereYear('dataPedidoFaturado', $anoAnterior)
            ->whereMonth('dataPedidoFaturado', $mesAnterior)
            ->sum('valorFaturadoPedido');

        return response()->json([
            'faturamentoMesAnterior' => $faturamentoMesAnterior
        ]);
    }

    public function getPedidos(Request $request)
    {
        $filial = $request->input('filial');

        $query = DB::table('vendas_clientes')->distinct();

        if (!empty($filial) && $filial !== 'Todas') {
            $query->where('filial', $filial);
        }

        $totalPedidos = $query->count('numeroPedido');

        $totalPedidosMesAtual = $query->whereMonth('dataPedido', now()->month)
            ->whereYear('dataPedido', now()->year)
            ->count('numeroPedido');

        return response()->json([
            'totalPedidos' => $totalPedidos,
            'totalPedidosMesAtual' => $totalPedidosMesAtual
        ]);
    }

    public function getPedidosMesAnterior(Request $request)
    {
        $anoAtual = Carbon::now()->year;
        $mesAtual = Carbon::now()->month;

        $mesAnterior = $mesAtual === 1 ? 12 : $mesAtual - 1;
        $anoAnterior = $mesAnterior === 12 ? $anoAtual - 1 : $anoAtual;

        $filial = $request->input('filial');

        $query = DB::table('vendas_clientes');

        if (!empty($filial) && $filial !== 'Todas') {
            $query->where('filial', $filial);
        }

        $totalPedidosMesAnterior = $query->whereYear('dataPedido', $anoAnterior)
            ->whereMonth('dataPedido', $mesAnterior)
            ->count('numeroPedido');

        return response()->json([
            'totalPedidosMesAnterior' => $totalPedidosMesAnterior
        ]);
    }

    public function rankingClientes(Request $request)
    {
        $filial = $request->input('filial');

        $query = DB::table('vendas_clientes')
            ->select('nomeFantasiaCliente', DB::raw('SUM(valorFaturadoPedido) as totalFaturado'), DB::raw('COUNT(numeroPedido) as totalPedidos'))
            ->whereMonth('dataPedido', date('m'))
            ->whereYear('dataPedido', date('Y'))
            ->groupBy('nomeFantasiaCliente')
            ->orderByDesc('totalFaturado')
            ->limit(5);

        if (!empty($filial) && $filial !== 'Todas') {
            $query->where('filial', $filial);
        }

        $ranking = $query->get();

        return response()->json($ranking);
    }

    public function rankingProdutos(Request $request)
    {
        $filial = $request->input('filial');

        $query = DB::table('vendas_produtos')
            ->select(
                'produto',
                DB::raw('SUM(quantidade) as totalVendido'),
                DB::raw('SUM(subTotalPedido) as totalFaturado')
            )
            ->whereMonth('dataPedido', date('m'))
            ->whereYear('dataPedido', date('Y'))
            ->groupBy('produto')
            ->orderByDesc('totalFaturado')
            ->limit(5);

        if (!empty($filial) && $filial !== 'Todas') {
            $query->where('filial', $filial);
        }

        $produtos = $query->get();

        return response()->json($produtos);
    }

    public function rankingCompletoClientes(Request $request)
    {
        $filial = $request->input('filial');
        $dataInicial = $request->input('dataInicial');
        $dataFinal = $request->input('dataFinal');

        $ultimoDiaMesAtual = Carbon::now()->endOfMonth()->toDateString();

        if (empty($dataInicial)) {
            $dataInicial = Carbon::now()->startOfMonth()->toDateString();
        }

        if (empty($dataFinal)) {
            $dataFinal = $ultimoDiaMesAtual;
        }

        if ($dataFinal < $dataInicial) {
            return response()->json(['error' => 'A data final não pode ser menor que a data inicial.'], 400);
        }

        if ($dataFinal > $ultimoDiaMesAtual) {
            $dataFinal = $ultimoDiaMesAtual;
        }

        $query = DB::table('vendas_clientes')
            ->select(
                'nomeFantasiaCliente',
                DB::raw('SUM(valorFaturadoPedido) as totalFaturado'),
                DB::raw('COUNT(numeroPedido) as totalPedidos')
            )
            ->whereBetween('dataPedido', [$dataInicial, $dataFinal]);

        if (!empty($filial) && $filial !== 'Todas') {
            $query->where('filial', $filial);
        }

        $ranking = $query
            ->groupBy('nomeFantasiaCliente')
            ->orderByDesc('totalFaturado')
            ->get();

        return response()->json($ranking);
    }

    public function rankingCompletoProdutos(Request $request)
    {
        $mesAtual = Carbon::now()->month;
        $anoAtual = Carbon::now()->year;

        $dataInicial = $request->input('dataInicial', Carbon::now()->startOfMonth()->toDateString());
        $dataFinal = $request->input('dataFinal', Carbon::now()->endOfMonth()->toDateString());
        $filial = $request->input('filial');

        $query = DB::table('vendas_produtos')
            ->select(
                'produto',
                DB::raw('SUM(subTotalPedido) as totalVendido'),
                DB::raw('SUM(quantidade) as totalPedidos')
            )
            ->whereBetween('dataPedido', [$dataInicial, $dataFinal]);

        if (!empty($filial) && $filial !== 'Todas') {
            $query->where('filial', $filial);
        }

        $ranking = $query
            ->groupBy('produto')
            ->orderByDesc('totalVendido')
            ->get();

        return response()->json($ranking);
    }

    public function getUltimosPedidos()
    {
        $ultimosPedidos = DB::table('vendas_clientes')
            ->select('nomeFantasiaCliente', 'valorFaturadoPedido', 'dataPedido')
            ->orderBy('dataPedido', 'desc')
            ->limit(10)
            ->get();

        return response()->json($ultimosPedidos);
    }
}
