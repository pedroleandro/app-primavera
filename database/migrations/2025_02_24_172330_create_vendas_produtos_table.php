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
        Schema::create('vendas_produtos', function (Blueprint $table) {
            $table->integer('numeroPedido');
            $table->date('dataPedido');
            $table->integer('codigoCliente')->nullable();
            $table->string('nomeCliente', 255)->nullable();
            $table->integer('codigoProduto');
            $table->string('produto', 255)->nullable();
            $table->integer('quantidade');
            $table->decimal('precoVenda', 10, 2);
            $table->decimal('subTotalPedido', 10, 2);
            $table->integer('codigoFilial')->nullable();
            $table->string('filial', 255)->nullable();

            $table->unique(['numeroPedido', 'codigoProduto']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas_produtos');
    }
};
