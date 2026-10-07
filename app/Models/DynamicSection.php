<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicSection extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'dynamic_section_products', 'dynamic_section_id', 'product_id')
            ->withPivot('position')
            ->orderByPivot('position', 'asc')
            ->withTimestamps();
    }

    public function section_products()
    {
        return $this->hasMany(DynamicSectionProduct::class, 'dynamic_section_id')->orderBy('position', 'asc');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }
}
