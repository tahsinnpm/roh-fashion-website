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
        'review_images'   => 'array',
        'faqs'            => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getEmailFieldMode(): string
    {
        $mode = strtolower(trim((string) ($this->email_field ?? ($this->about_cards['email_field'] ?? 'hidden'))));
        if (in_array($mode, ['required', 'req'])) {
            return 'required';
        }
        if (in_array($mode, ['optional', 'unrequired', 'opt'])) {
            return 'optional';
        }
        return 'hidden';
    }
}
