<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class JemaahDummySeeder extends Seeder
{
    public function run(): void
    {
        // HAPUS 30 AKUN LAMA (jika ada)
        $deletedCount = 0;
        for ($i = 1; $i <= 30; $i++) {
            $email = 'jemaah' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '@gmail.com';
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->forceDelete();
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->command?->info("🗑️ Berhasil menghapus {$deletedCount} user jemaah lama!");
        } else {
            $this->command?->info("ℹ️ Tidak ada user jemaah lama yang ditemukan, langsung membuat baru.");
        }

        // BUAT 30 AKUN BARU
        $names = [
            'Ahmad Fauzan', 'Siti Aisyah', 'Budi Santoso', 'Dewi Lestari', 'Rizky Maulana',
            'Nurul Hidayah', 'Rina Kartika', 'Muhammad Iqbal', 'Halimah Tusadiah', 'Agus Pratama',
            'Yuni Marlina', 'Fajar Nugraha', 'Nadia Putri', 'Hendra Wijaya', 'Fitri Amalia',
            'Wahyu Triyono', 'Rani Aulia', 'Bagus Saputra', 'Maya Salsabila', 'Dian Permata',
            'Andi Hakim', 'Rina Safitri', 'Doni Setiawan', 'Nia Kurnia', 'Rudi Hartono',
            'Lina Marlina', 'Taufik Hidayat', 'Sari Dewi', 'Irfan Maulana', 'Nina Anjani'
        ];

        $startDate = Carbon::now()->subDays(30);

        foreach ($names as $index => $name) {
            $number = $index + 1;
            $email = 'jemaah' . str_pad((string) $number, 2, '0', STR_PAD_LEFT) . '@gmail.com';

            $randomDays = rand(0, 30);
            $createdAt = $startDate->copy()->addDays($randomDays);
            $updatedAt = $createdAt->copy()->addHours(rand(1, 72));

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('12345678'),
                'role' => 'jemaah',
                'status' => 'proses',
                'email_verified_at' => null,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);
        }

        $this->command?->info('✅ Berhasil membuat 30 user jemaah baru!');
        $this->command?->info("📅 Rentang created_at: " . User::where('email', 'jemaah01@gmail.com')->first()->created_at . " sampai " . User::where('email', 'jemaah30@gmail.com')->first()->created_at);
    }
}
