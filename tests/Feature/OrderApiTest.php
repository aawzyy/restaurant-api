<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Table; 
use Laravel\Sanctum\Sanctum;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_place_an_order()
    {
        // 1. Siapkan User
        $user = User::factory()->create();
        
        // 2. Buat Meja
        $table = Table::create([
            'table_number' => '05', 
            'status' => 'available'
        ]);
        
        // 3. Buat Menu & Kategori
        $category = Category::create(['name' => 'Main Course', 'slug' => 'main']);
        
        $menu1 = Menu::create([
            'name' => 'Sate Ayam',
            'price' => 30000,
            'category_id' => $category->id,
            'is_available' => true,
            'image_path' => 'sate.jpg'
        ]);

        // 4. Login
        Sanctum::actingAs($user);

        // 5. Siapkan Payload
        $payload = [
            'table_id' => $table->id, 
            'total_price' => 60000,
            'items' => [
                [
                    'menu_id' => $menu1->id,
                    'quantity' => 2,
                    'price' => 30000,
                    // PERBAIKAN: Gunakan 'notes' (jamak) sesuai database
                    'notes' => 'Pedas banget' 
                ]
            ]
        ];

        // 6. Tembak API
        $response = $this->postJson('/api/orders', $payload);

        // 7. Cek Hasil Response
        $response->assertStatus(201)
                 ->assertJson(['message' => 'Pesanan berhasil dibuat']);

        // 8. Cek Database Orders
        $this->assertDatabaseHas('orders', [
            'table_id' => $table->id,
            'total_price' => 60000,
            'status' => 'pending'
        ]);

        // 9. Cek Database Order Items
        $this->assertDatabaseHas('order_items', [
            'menu_id' => $menu1->id,
            'quantity' => 2,
            // PERBAIKAN: Cek kolom 'notes' (jamak)
            'notes' => 'Pedas banget'
        ]);
    }
}