<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Gunakan DB Transaction agar jika satu gagal, semua batal (keamanan data)
        return \DB::transaction(function () use ($request) {
            $totalPrice = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $menu = \App\Models\Menu::find($item['menu_id']);
                $subTotal = $menu->price * $item['quantity'];
                $totalPrice += $subTotal;

                $orderItems[] = [
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $menu->price,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            $order = \App\Models\Order::create([
                'table_id' => $request->table_id,
                'user_id' => auth()->id(), // Ambil ID dari token login
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            $order->items()->createMany($orderItems);

            // Update status meja jadi occupied
            \App\Models\Table::find($request->table_id)->update(['status' => 'occupied']);

            return response()->json(['message' => 'Pesanan berhasil dibuat', 'data' => $order->load('items')], 201);
        });
    }

    // Menampilkan pesanan yang perlu dimasak (Pending & Processing)
    public function kitchenIndex()
    {
        $orders = \App\Models\Order::with(['items.menu', 'table'])
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['data' => $orders]);
    }

    // Update status (Misal: dari Pending ke Processing, lalu ke Done)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,done',
        ]);

        $order = \App\Models\Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        // Jika pesanan 'done', meja otomatis kosong lagi
        if ($request->status == 'done') {
            \App\Models\Table::find($order->table_id)->update(['status' => 'available']);
        }

        return response()->json(['message' => 'Status diperbarui', 'data' => $order]);
    }
}
