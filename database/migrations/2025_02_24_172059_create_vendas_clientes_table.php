<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendas_clientes', function (Blueprint $table) {
            $table->integer('numeroPedido')->primary();
            $table->date('dataPedido');
            $table->date('dataPedidoFaturado');
            $table->string('codigoCobranca', 10)->nullable();
            $table->integer('codigoFilial')->nullable();
            $table->string('filial', 100)->nullable();
            $table->integer('codigoCliente')->nullable();
            $table->string('nomeCliente', 255)->nullable();
            $table->string('nomeFantasiaCliente', 255)->nullable();
            $table->decimal('valorFaturadoPedido', 10, 2)->nullable();
            $table->decimal('valorDespesas', 10, 2)->nullable();
            $table->decimal('valorLiquidoPedido', 10, 2)->nullable();

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas_clientes');
    }
};
