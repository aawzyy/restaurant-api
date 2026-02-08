<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'menu_id', 'quantity', 'price_at_purchase', 'notes'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
