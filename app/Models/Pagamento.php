<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;
    protected $table = 'pagamento';
    public $timestamps = false;
    protected $fillable = [
        'nome_cliente',
        'cpf',
        'descricao',
        'valor',
        'status',
        'payment_method',
        'data_pagamento'
    ];

    public function payMeth(){
        return $this->hasOne(Payment_Method::class, 'id', 'payment_method');
    }
}
