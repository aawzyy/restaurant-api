<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache; // <--- JANGAN LUPA IMPORT INI

class Menu extends Model
{
    protected $fillable = ['category_id', 'name', 'price', 'image_path', 'is_available'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected static function booted(): void
    {
        // Saat Menu BARU dibuat atau DIUPDATE
        static::saved(function ($menu) {
            Cache::forget('menus_list'); // Hapus cache lama
        });

        // Saat Menu DIHAPUS
        static::deleted(function ($menu) {
            Cache::forget('menus_list'); // Hapus cache lama
        });
    }
}
