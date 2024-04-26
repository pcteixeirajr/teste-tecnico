<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Pagamento;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProcessPayController extends Controller
{
    public function processPayment(ProcessPaymentRequest $request): JsonResponse 
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $payment = Pagamento::with('payMeth')->find($request->payment_id);

        if (!$payment) {
            return response()->json(['message' => 'Pagamento não encontrado'], 404);
        }

        if ($payment->status === 'expired' || in_array($payment->status, ['paid', 'failed'])) { 
            $message = $payment->status === 'expired' ? 'expirado' : 'já processado';
            return response()->json(['message' => "Esse pagamento está $message"], 303);
        }

        $fee = $this->getFeeByPaymentMethod($payment->payMeth->slug); 

        $payment->status = $request->user()->increment('saldo', $payment->valor - ($payment->valor * $fee)) ? 'paid' : 'failed';
        $payment->save();

        $message = $payment->status === 'paid' ? 'aprovado' : 'recusado';
        return response()->json([
            'status' => "O pagamento foi $message",
            'saldo' => "O saldo atual é de: R$" . number_format($request->user()->saldo, 2, '.', ''), 
        ], $payment->status === 'paid' ? 200 : 400);
    }

    private function getFeeByPaymentMethod(string $slug): float
    {
        switch ($slug) {
            case 'pix':
                return 0.15;
            case 'boleto':
                return 0.2;
            case 'bank_transfer':
                return 0.4;
            default:
                return 0; 
        }
    }
}
