<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProcessPayController extends Controller
{
    public function proccessPayment(Request $request){
        $body = $request->all();
        $validator = Validator::make($body, [ //validação do formulario
            'payment_id' => ['required', 'numeric']
        ]);
        if ($validator->fails()) { //retorno com os erros caso exista algum
            return response()->json($validator->errors(), 400);
        }

        if(rand(0,99) <= 69){
            $pay = true;
        }else{
            $pay = false;
        }
        $payment = Pagamento::where('id', $body['payment_id'])->first()->load('payMeth');
        $user = $request->user();

        $dtAtual = date('Y-m-d');
        $tsDtDb = strtotime($payment->data_pagamento);
        $tsDtAt = strtotime($dtAtual);
        if ($tsDtAt > $tsDtDb) {
            $payment->status = 'expired';
            $payment->save();
            return response()->json(['message' => 'Esse pagamento está expirado'],303);
        }

        if($payment->status == 'paid' || $payment->status == 'failed'){
            return response()->json(['message' => 'Esse pagamento já foi processado'],303);
        }

        switch ($payment->payMeth->slug) {
            case 'pix':
                $payMethod = 0.15;
                break;
            case 'boleto':
                $payMethod = 0.2;
                break;
            case 'bank_transfer':
                $payMethod = 0.4;
                break;
        }
        if($pay){
            $payment->status = 'paid';
            
            $value = $payment->valor - ($payment->valor * $payMethod);
            $user->saldo += $value;
            $user->save();
            $message = 'aprovado';
            $code = 200;
        }else{
            $payment->status = 'failed';
            $message = 'recusado';
            $code = 400;
        }
        $payment->save();
        
        return response()->json(
            [
                'status' => 'O pagamento foi '.$message,
                'saldo' => 'O saldo atual é de: R$'.$user->saldo
            ],$code
        );

    }
}
