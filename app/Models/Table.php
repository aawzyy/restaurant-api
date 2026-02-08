<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    // Mengizinkan pengisian data kolom ini
    protected $fillable = ['table_number', 'status'];

    /**
     * Relasi: Satu meja bisa memiliki banyak pesanan (History Order)
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}