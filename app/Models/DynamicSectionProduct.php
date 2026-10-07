<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicSectionProduct extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function dynamic_section()
    {
        return $this->belongsTo(DynamicSection::class, 'dynamic_section_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
