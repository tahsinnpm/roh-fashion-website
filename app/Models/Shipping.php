<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getPriceAttribute()
    {
        return $this->shipping_charge;
    }

    public function getTitleAttribute()
    {
        if ($this->type == 1) {
            return session('language') === 'bangla' ? 'ঢাকার ভিতরে  ডেলিভারি চার্জ' : 'ঢাকার ভিতরে ডেলিভারি চার্জ';
        }
        if ($this->type == 2) {
            return session('language') === 'bangla' ? 'ঢাকার বাহিরে হোম ডেলিভারি চার্জ' : 'ঢাকার বাহিরে ডেলিভারি চার্জ';
        }
        return $this->name;
    }
}
