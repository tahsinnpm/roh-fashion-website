<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbandonedCheckout extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'cart_snapshot' => 'array',
        'last_activity_at' => 'datetime',
    ];
}
