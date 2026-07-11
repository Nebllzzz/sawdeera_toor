<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JemaahRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_jemaah_can_register_with_short_form_and_waits_for_activation(): void
    {
        $response = $this->post('/actionregister', [
            'name' => 'Ahmad Fauzan',
            'email' => 'ahmad@example.com',
            'no_telepon' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => 'ahmad@example.com',
            'status' => 'proses',
        ]);
        $this->assertDatabaseHas('data_jemaah', [
            'no_telepon' => '081234567890',
            'status_data' => 'belum_lengkap',
        ]);

        $this->post('/actionlogin', [
            'email' => 'ahmad@example.com',
            'password' => 'rahasia123',
        ])->assertSessionHas('gagal');
    }

    public function test_activated_jemaah_can_login(): void
    {
        User::factory()->create([
            'email' => 'aktif@example.com',
            'password' => bcrypt('rahasia123'),
            'role' => 'jemaah',
            'status' => 'aktif',
        ]);

        $this->post('/actionlogin', [
            'email' => 'aktif@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
    }
}
