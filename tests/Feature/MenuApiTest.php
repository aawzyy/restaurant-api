<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Menu;
use Laravel\Sanctum\Sanctum;

class MenuApiTest extends TestCase
{
    use RefreshDatabase; // Ini akan reset database khusus testing (aman)

    /**
     * Test apakah user yang login bisa melihat daftar menu.
     */
    public function test_authenticated_user_can_fetch_menu_list()
    {
        // 1. Kita siapkan User Pura-pura
        $user = User::factory()->create();

        // 2. Kita siapkan Kategori & Menu Pura-pura
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-cat']);
        Menu::create([
            'name' => 'Nasi Goreng Test',
            'price' => 25000,
            'category_id' => $category->id,
            'is_available' => true,
            'image_path' => 'dummy.jpg'
        ]);

        // 3. Login Pura-pura pakai Sanctum
        Sanctum::actingAs($user);

        // 4. Akses Endpoint API
        $response = $this->getJson('/api/menus');

        // 5. Harapannya: Status 200 (OK) dan ada data 'Nasi Goreng Test'
        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Nasi Goreng Test']);
    }

    /**
     * Test apakah user yang BELUM login ditolak server.
     */
    public function test_guest_cannot_fetch_menu_list()
    {
        // Langsung tembak API tanpa login
        $response = $this->getJson('/api/menus');

        // Harapannya: Status 401 (Unauthorized)
        $response->assertStatus(401);
    }
}   