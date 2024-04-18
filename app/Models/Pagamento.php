<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;
    protected $table = 'pagamento';
    protected $fillable = [
        'nome_cliente',
        'cpf',
        'descricao',
        'valor',
        'status',
        'payment_method_slug',
        'data_pagamento'
    ];
}
