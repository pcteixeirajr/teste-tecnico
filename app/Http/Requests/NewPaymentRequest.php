<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome_cliente' => ['required', 'string'],
            'cpf' => ['required', 'string'],
            'descricao' => ['required', 'string'],
            'valor' => ['required', 'numeric'],
            'status' => ['required','in:pending,paid,expired,failed'],
            'payment_method_slug' => ['required', 'in:pix,boleto,bank_transfer'],
            'data_pagamento' => ['required', 'date']
        ];
    }
}
