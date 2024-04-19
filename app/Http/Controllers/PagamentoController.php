<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPaymentRequest;
use App\Models\Pagamento;
use App\Models\Payment_Method;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PagamentoController extends Controller
{
    public function newPayment (Request $request){
        $body = $request->all();
        $validator = Validator::make($body, [ //validação do formulario
            'nome_cliente' => ['required', 'string'],
            'cpf' => ['required', 'string'],
            'descricao' => ['required', 'string'],
            'valor' => ['required', 'numeric'],
            'status' => ['required','in:pending,paid,expired,failed'],
            'payment_method' => ['required'],
            'data_pagamento' => ['required', 'date']
        ]);
        if ($validator->fails()) { //retorno com os erros caso exista algum
            return response()->json($validator->errors(), 400);
        }
        $payMethod = Payment_Method::where('slug', $body['payment_method'])->first()->id; 
        $payment = new Pagamento();
        $payment->fill($body);
        $payment->payment_method = $payMethod;
        $payment->save();
        return response()->json([
            'message' => 'Novo pagamento registrado com sucesso!'
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
                'Status' => $item->status,
                'Data do pagamento' => $item->data_pagamento
            ]);
        }
        return response()->json($allPayments);
    }

    public function getSinglePayment ($id){
        $singlePayment = Pagamento::where('id', $id)->first()->load('payMeth');
        return response()->json([
            'Id' => $singlePayment->id,
            'Nome do cliente' => $singlePayment->nome_cliente,
            'CPF' => $singlePayment->cpf,
            'Descrição' => $singlePayment->descricao,
            'Valor' => $singlePayment->valor,
            'Status' => $singlePayment->status,
            'Payment Method' => $singlePayment->payMeth->slug,
            'Data de pagamento' => $singlePayment->data_pagamento
        ]);
    }

}
