<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\PagamentoController;
use App\Models\Pagamento;
use App\Models\Payment_Method;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PagamentoControllerTest extends TestCase
{
    use RefreshDatabase; // Clears database before each test

    public function testNewPayment_withValidData_createsPaymentAndReturnsSuccessResponse()
    {
        $paymentMethod = Payment_Method::factory()->create();
        $data = [
            'nome_cliente' => 'Cliente Teste',
            'cpf' => '12345678900',
            'descricao' => 'Pagamento de teste',
            'valor' => 100.00,
            'status' => 'pending',
            'payment_method' => $paymentMethod->slug,
            'data_pagamento' => '2024-04-27',
        ];

        $response = $this->json('POST', '/api/pagamentos/novo', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message' => 'Novo pagamento registrado com sucesso!',
            ]);

        $this->assertDatabaseHas('pagamentos', [
            'nome_cliente' => $data['nome_cliente'],
            'valor' => $data['valor'],
            'status' => $data['status'],
            'payment_method' => $paymentMethod->id,
        ]);
    }

    public function testNewPayment_withInvalidData_returnsBadRequestResponse()
    {
        $data = [
            // Missing required fields
            'descricao' => 'Pagamento de teste',
            'valor' => 100.00,
            'data_pagamento' => '2024-04-27',
        ];

        $response = $this->json('POST', '/api/pagamentos/novo', $data);

        $response->assertStatus(400)
            ->assertJsonValidationErrors([
                'nome_cliente' => 'required',
                'cpf' => 'required',
                'status' => 'required',
                'payment_method' => 'required',
            ]);

        $this->assertDatabaseMissing('pagamentos', $data);
    }

    public function testGetPayments_returnsAllPaymentsAsJson()
    {
        $payments = Pagamento::factory()->count(3)->create();

        $response = $this->json('GET', '/api/pagamentos');

        $response->assertStatus(200)
            ->assertJsonCount($payments->count())
            ->assertJsonStructure([
                '*' => [
                    'ID',
                    'Nome do cliente',
                    'Valor',
                    'Status',
                    'Data do pagamento',
                ],
            ]);
    }

    public function testGetSinglePayment_withValidId_returnsPaymentDetails()
    {
        $paymentMethod = Payment_Method::factory()->create();
        $payment = Pagamento::factory()->create([
            'payment_method' => $paymentMethod->id,
        ]);

        $response = $this->json('GET', "/api/pagamentos/$payment->id");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'Id',
                'Nome do cliente',
                'CPF',
                'Descrição',
                'Valor',
                'Status',
                'Payment Method',
                'Data de pagamento',
            ])
            ->assertJson([
                'Id' => $payment->id,
                'Nome do cliente' => $payment->nome_cliente,
                'CPF' => $payment->cpf,
                'Descrição' => $payment->descricao,
                'Valor' => $payment->valor,
                'Status' => $payment->status,
                'Payment Method' => $paymentMethod->slug,
                'Data de pagamento' => $payment->data_pagamento,
            ]);
    }

    public function testGetSinglePayment_withInvalidId_returnsNotFoundResponse()
    {
        $response = $this->json('GET', '/api/pagamentos/123'); // Non-existent ID

        $response->assertStatus(404);
    }
}
