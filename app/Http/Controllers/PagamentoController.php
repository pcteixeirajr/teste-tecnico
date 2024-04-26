<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPaymentRequest;
use App\Models\Pagamento;
use App\Models\Payment_Method;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PagamentoController extends Controller
{
    public function newPayment(NewPaymentRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nome_cliente' => ['required', 'string'],
            'cpf' => ['required', 'string'],
            'descricao' => ['required', 'string'],
            'valor' => ['required', 'numeric'],
            'status' => ['required', 'in:pending,paid,expired,failed'],
            'payment_method' => ['required'],
            'data_pagamento' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $payment = new Pagamento($request->all());
        $payment->payment_method = Payment_Method::where('slug', $payment->payment_method)->first()->id;
        $payment->save();

        return response()->json([
            'message' => 'Novo pagamento registrado com sucesso!',
        ], 201);
    }

    public function getPayments(): JsonResponse
    {
        $payments = Pagamento::select([
            'id',
            'nome_cliente',
            'valor',
            'status',
            'data_pagamento',
        ])->get();

        return response()->json($payments);
    }

    public function getSinglePayment(int $id): JsonResponse
    {
        $singlePayment = Pagamento::with('payMeth')->find($id);

        return response()->json([
            'Id' => $singlePayment->id,
            'Nome do cliente' => $singlePayment->nome_cliente,
            'CPF' => $singlePayment->cpf,
            'Descrição' => $singlePayment->descricao,
            'Valor' => $singlePayment->valor,
            'Status' => $singlePayment->status,
            'Payment Method' => $singlePayment->payMeth->slug,
            'Data de pagamento' => $singlePayment->data_pagamento,
        ]);
    }
}
