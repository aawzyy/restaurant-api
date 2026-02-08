<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Table; // Import Model Table
use Laravel\Sanctum\Sanctum;

class KitchenApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_kitchen_staff_can_update_order_status()
    {
        // 1. Siapkan User (Staff Dapur)
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // 2. Buat Meja Dulu (FIX: Pakai 'table_number', bukan 'number')
        $table = Table::create([
            'table_number' => '10', // <--- Sesuai nama kolom di database
            'status' => 'occupied'
        ]);

        // 3. Buat Order Dummy dengan ID Meja yang benar
        $order = Order::create([
            'user_id' => $user->id,
            'table_id' => $table->id, // <--- Relasikan ke ID meja
            'total_price' => 50000,
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        // 4. Tembak API untuk ubah status jadi 'processing' (Sedang Dimasak)
        // Sesuaikan route ini dengan api.php kamu (misal: /api/orders/{id}/status)
        $response = $this->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'processing'
        ]);

        // 5. Cek Response Sukses (200 OK)
        $response->assertStatus(200);

        // 6. Cek Database: Pastikan status benar-benar berubah
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing'
        ]);
    }
}