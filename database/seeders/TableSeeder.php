<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kosongkan dulu biar tidak error "Duplicate Entry" kalau dijalankan ulang
        // Table::truncate(); // Hati-hati, ini menghapus semua data meja lama! Gunakan jika masih development.

        // 2. Cek apakah sudah ada data? Kalau kosong, baru isi.
        if (Table::count() == 0) {
            for ($i = 1; $i <= 10; $i++) {
                Table::create([
                    'table_number' => 'M-' . str_pad($i, 2, '0', STR_PAD_LEFT), // Jadi: M-01, M-02...
                    'status' => 'available'
                ]);
            }
            $this->command->info('✅ Berhasil membuat 10 meja (M-01 s/d M-10)!');
        } else {
            $this->command->warn('⚠️ Data meja sudah ada, seeding dilewati.');
        }
    }
}