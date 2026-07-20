<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operators = [
            [
                'name' => 'Operator Satu',
                'email' => 'operator1@sawdeera.co.id',
            ],
            [
                'name' => 'Operator Dua',
                'email' => 'operator2@sawdeera.co.id',
            ],
        ];

        foreach ($operators as $operator) {
            DB::table('users')->updateOrInsert(
                ['email' => $operator['email']],
                [
                    'name' => $operator['name'],
                    'password' => Hash::make('operator123'),
                    'role' => 'operator',
                    'status' => 'aktif',
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
