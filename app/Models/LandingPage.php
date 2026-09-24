<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $table = 'landing_pages';

    protected $guarded = ['id'];

    protected $casts = [
        'slider_images'   => 'array',
        'features'        => 'array',
        'overview_points' => 'array',
        'about_cards'     => 'array',
        'reviews'         => 'array',
        'faqs'            => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
