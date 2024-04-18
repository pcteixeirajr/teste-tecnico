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
        Schema::create('pagamento', function (Blueprint $table) {
            $table->id();
            $table->string('nome_cliente');
            $table->string('cpf');
            $table->string('descricao');
            $table->float('valor', precision: 2);
            $table->enum('status', ['pending', 'paid', 'expired', 'failed']);
            $table->string('payment_method_slug');
            $table->date('data_pagamento');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamento');
    }
};
