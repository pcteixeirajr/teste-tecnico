<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Pagamento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProcessPayControllerTest extends TestCase
{
    use RefreshDatabase; // Clears database before each test

    public function testProcessPayment_withValidPaymentId_andSuccess_returnsSuccessResponse()
    {
        $user = User::factory()->create();
        $paymentMethod = Payment_Method::factory()->create(['slug' => 'pix']);
        $payment = Pagamento::factory()->create([
            'payment_method' => $paymentMethod->id,
            'status' => 'pending',
            'data_pagamento' => date('Y-m-d', strtotime('-1 day')), // One day before today
            'valor' => 100.00,
        ]);

        $this->actingAs($user); // Simulate authenticated user

        $response = $this->json('POST', "/api/pagamentos/processar/$payment->id");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'saldo',
            ])
            ->assertJson([
                'status' => 'O pagamento foi aprovado',
                'saldo' => 'O saldo atual é de: R$' . ($user->saldo + (100.00 - (100.00 * 0.15))), // Consider 15% fee for PIX
            ]);

        $payment->refresh(); // Reload payment data from database
        $this->assertEquals('paid', $payment->status);
        $this->assertGreaterThan($user->saldo, $payment->valor); // User saldo should increase
    }

    public function testProcessPayment_withValidPaymentId_andFailure_returnsFailureResponse()
    {
        $user = User::factory()->create();
        $paymentMethod = Payment_Method::factory()->create(['slug' => 'pix']);
        $payment = Pagamento::factory()->create([
            'payment_method' => $paymentMethod->id,
            'status' => 'pending',
            'data_pagamento' => date('Y-m-d', strtotime('-1 day')), // One day before today
            'valor' => 100.00,
        ]);

        $this->actingAs($user); // Simulate authenticated user

        // Simulate payment processor failure (modify logic to always return false)
        $mock = Mockery::mock('overload:' . ProcessPayController::class);
        $mock->shouldReceive('proccessPayment')->once()->andReturn(response()->json(['message' => 'Pagamento recusado pelo processador'], 400));
        $this->app->instance(ProcessPayController::class, $mock);

        $response = $this->json('POST', "/api/pagamentos/processar/$payment->id");

        $response->assertStatus(400)
            ->assertJsonStructure([
                'message',
            ])
            ->assertJson([
                'message' => 'Pagamento recusado pelo processador',
            ]);

        $payment->refresh(); // Reload payment data from database
        $this->assertEquals('failed', $payment->status);
    }

    public function testProcessPayment_withExpiredPayment_returnsExpiredResponse()
    {
        $user = User::factory()->create();
        $paymentMethod = Payment_Method::factory()->create(['slug' => 'pix']);
        $payment = Pagamento::factory()->create([
            'payment_method' => $paymentMethod->id,
            'status' => 'pending',
            'data_pagamento' => date('Y-m-d', strtotime('-2 day')), // Two days before today (expired)
            'valor' => 100.00,
        ]);

        $this->actingAs($user); // Simulate authenticated user

        $response = $this->json('POST', "/api/pagamentos/processar/$payment->id");

        $response->assertStatus(303)
            ->assertJsonStructure([
                'message',
            ])
            ->assertJson([
                'message' => 'Esse pagamento está expirado',
            ]);

        $payment->refresh(); // Reload payment data from database
        $this->assertEquals('expired', $payment->status);
    }

    public function testProcessPayment_with
}