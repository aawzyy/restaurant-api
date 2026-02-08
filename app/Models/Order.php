<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = ['table_id', 'user_id', 'total_price', 'status'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
    
    // TAMBAHKAN JUGA (Opsional tapi bagus): Relasi ke User/Waiter
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
