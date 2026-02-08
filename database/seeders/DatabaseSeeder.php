<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Table;
use App\Models\Category;
use App\Models\Menu;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. BUAT USER ADMIN (PENTING BUAT LOGIN FILAMENT)
        // Cek dulu biar gak duplikat error
        if (!User::where('email', 'fauziosama14@gmail.com')->exists()) {
            User::create([
                'name' => 'Fauzi Osama',
                'email' => 'fauziosama14@gmail.com',
                'password' => Hash::make('password123'), // Password default: 'password'
                
            ]);
            $this->command->info('✅ Admin User created: fauziosama14@gmail.com | pass: password');
        }

        // 2. BUAT MEJA (TABLES) - 10 MEJA
        // Kita kosongkan dulu tabel biar bersih (Opsional)
        // Table::truncate(); 
        
        for ($i = 1; $i <= 10; $i++) {
            // Format nomor meja: 01, 02, ... 10
            $noMeja = str_pad($i, 2, '0', STR_PAD_LEFT);
            
            Table::firstOrCreate(
                ['table_number' => $noMeja],
                ['status' => 'available']
            );
        }
        $this->command->info('✅ 10 Tables created.');

        // 3. BUAT KATEGORI & MENU
        $kategoriMakanan = Category::firstOrCreate(['name' => 'Makanan Berat']);
        $kategoriMinuman = Category::firstOrCreate(['name' => 'Minuman']);
        $kategoriCemilan = Category::firstOrCreate(['name' => 'Cemilan']);

        // Menu Makanan
        Menu::firstOrCreate(['name' => 'Nasi Goreng Spesial'], [
            'price' => 25000,
            'category_id' => $kategoriMakanan->id,
            'is_available' => true,
            'image_path' => 'menus/nasigoreng.jpg', // Dummy path
        ]);

        Menu::firstOrCreate(['name' => 'Sate Ayam Madura'], [
            'price' => 30000,
            'category_id' => $kategoriMakanan->id,
            'is_available' => true,
            'image_path' => 'menus/sateayam.jpg',
        ]);

        // Menu Minuman
        Menu::firstOrCreate(['name' => 'Es Teh Manis'], [
            'price' => 5000,
            'category_id' => $kategoriMinuman->id,
            'is_available' => true,
            'image_path' => 'menus/esteh.jpg',
        ]);

        Menu::firstOrCreate(['name' => 'Kopi Susu Gula Aren'], [
            'price' => 18000,
            'category_id' => $kategoriMinuman->id,
            'is_available' => true,
            'image_path' => 'menus/kopisusu.jpg',
        ]);

        // Menu Cemilan
        Menu::firstOrCreate(['name' => 'Kentang Goreng'], [
            'price' => 15000,
            'category_id' => $kategoriCemilan->id,
            'is_available' => true,
            'image_path' => 'menus/kentang.jpg',
        ]);
        
        $this->command->info('✅ Categories & Menus created.');
    }
}