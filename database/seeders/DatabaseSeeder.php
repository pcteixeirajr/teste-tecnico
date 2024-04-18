<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'usuário 1',
            'email' => 'user.@example.com',
            'password' => 'senha#123',
            'saldo' => '0.00'
        ]);

        DB::table('payment_method')->insert(
            [
                'name' => 'PIX',
                'slug' => 'pix',
            ]
        );
        DB::table('payment_method')->insert(
            [
                'name' => 'Boleto',
                'slug' => 'boleto',
            ]
        );
        DB::table('payment_method')->insert(
            [
                'name' => 'Transferencia bancaria',
                'slug' => 'bank_transfer'
            ]
        );
    }
}
