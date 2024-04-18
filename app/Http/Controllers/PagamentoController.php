<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPaymentRequest;
use App\Models\Pagamento;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    public function newPayment (NewPaymentRequest $request){
        $body = $request->all();
        $payment = new Pagamento();
        $payment->fill($body);
        $payment->save();
        return response()->json([
            'Novo pagamento registrado com sucesso!',
            'Detalhes' => $payment
        ], 201);
    }

    public function getPayments (){
        $payments = Pagamento::all();
        $allPayments = [];
        foreach ($payments as $item){
            array_push($allPayments, [
                'ID' => $item->id,
                'Nome do cliente' => $item->nome_cliente,
                'Valor' => $item->valor,
                'Data do pagamento' => $item->data_pagamento
            ]);
        }
        return response()->json($allPayments);
    }

    public function getSinglePayment ($id){
        $singlePayment = Pagamento::where('id', $id)->first();
        return response()->json($singlePayment);
    }
}
