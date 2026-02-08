<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase; // Tambahkan ini
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_user_cannot_login_with_wrong_password()
    {
        $user = \App\Models\User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_triggers_otp_generation()
    {
        $user = \App\Models\User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertNotNull($user->refresh()->otp_code);
    }

    public function test_authenticated_user_can_view_menus()
    {
        // 1. Buat User & Login palsu (actingAs)
        $user = \App\Models\User::factory()->create();

        // 2. Buat dummy category & menu
        $category = \App\Models\Category::create(['name' => 'Food']);
        \App\Models\Menu::create([
            'category_id' => $category->id,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'image_path' => 'dummy.jpg',
            'is_available' => true,
        ]);

        // 3. Request ke endpoint menu dengan Token
        $response = $this->actingAs($user)->getJson('/api/menus');

        // 4. Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'price']]]);
    }
}
