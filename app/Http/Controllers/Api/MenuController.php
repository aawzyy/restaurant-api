<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function index()
    {
        // Ambil data menu dari cache atau DB
        $menus = Cache::remember('menus_list', 3600, function () {
            return Menu::with('category')->get();
        });

        $formattedMenus = $menus->map(function ($menu) {
            // Perbaikan Logika: Cek apakah path ada, bukan string "0", dan bukan null
            if (!empty($menu->image_path) && $menu->image_path !== "0") {
                $url = Storage::disk('s3')->url($menu->image_path);
                
                // Gunakan HOST yang memanggil API agar sinkron dengan IP Laptop
                $serverHost = request()->getHost();
                $menu->image_url = str_replace(['127.0.0.1', 'localhost'], $serverHost, $url);
            } else {
                // JIKA FOTO KOSONG / "0": Berikan Placeholder cantik agar UI tidak kosong
                // Menggunakan UI Avatars untuk membuat inisial nama menu sebagai gambar
                $menu->image_url = "https://ui-avatars.com/api/?name=" . urlencode($menu->name) . "&background=random&color=fff&size=512";
            }
            return $menu;
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedMenus
        ]);
    }

    public function categories()
    {
        $categories = Category::all();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Simpan ke MinIO
        $path = $request->file('image')->store('menu-images', 's3');

        $menu = Menu::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'image_path' => $path,
            'is_available' => true,
        ]);

        // Hapus cache agar data baru muncul
        Cache::forget('menus_list');

        return response()->json([
            'status' => 'success',
            'message' => 'Menu berhasil ditambahkan!',
            'data' => $menu,
        ], 201);
    }
}