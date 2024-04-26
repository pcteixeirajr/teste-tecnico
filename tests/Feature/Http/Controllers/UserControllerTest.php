<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase; // Clears database before each test

    public function testLogin_withValidCredentials_returnsSuccessResponse()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('secretpassword'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'secretpassword',
        ];

        $response = $this->json('POST', '/api/login', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'saldo',
                'token',
                'token_type',
                'expires_in',
            ]);

        $this->assertAuthenticated(); // Check if user is logged in
    }

    public function testLogin_withInvalidCredentials_returnsErrorResponse()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('secretpassword'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->json('POST', '/api/login', $data);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'message',
            ])
            ->assertJson([
                'message' => 'credenciais inválidas',
            ]);

        $this->assertGuest(); // Check if user is not logged in
    }

    public function testLogin_withMissingEmail_returnsBadRequestResponse()
    {
        $data = [
            'password' => 'secretpassword',
        ];

        $response = $this->json('POST', '/api/login', $data);

        $response->assertStatus(400)
            ->assertJsonValidationErrors([
                'email' => 'required',
            ]);
    }

    public function testLogin_withMissingPassword_returnsBadRequestResponse()
    {
        $data = [
            'email' => 'test@example.com',
        ];

        $response = $this->json('POST', '/api/login', $data);

        $response->assertStatus(400)
            ->assertJsonValidationErrors([
                'password' => 'required',
            ]);
    }
}
