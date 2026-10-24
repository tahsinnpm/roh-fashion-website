<?php

namespace App\Services;

use App\Models\AbandonedCheckout;
use Illuminate\Support\Carbon;

class AbandonedCheckoutCapture
{
    public static function normalizePhone(?string $phone): string
    {
        $phone = preg_replace('/[\s()-]+/', '', (string) $phone);
        return preg_replace('/^(?:\+88|88)(01)/', '$1', $phone);
    }

    public function capture(array $data): AbandonedCheckout
    {
        $phone = self::normalizePhone($data['phone']);
        $lead = AbandonedCheckout::firstOrNew(['phone' => $phone]);

        $lead->fill([
            'name' => $data['name'] ?? $lead->name,
            'email' => $data['email'] ?? $lead->email,
            'address' => $data['address'] ?? $lead->address,
            'source' => $data['source'],
            'landing_slug' => $data['landing_slug'] ?? null,
            'cart_snapshot' => $data['cart_snapshot'] ?? $lead->cart_snapshot,
            'status' => 'pending',
            'order_id' => null,
            'last_activity_at' => Carbon::now(),
        ])->save();

        return $lead;
    }

    public function markConverted(?string $phone, int $orderId): void
    {
        $phone = self::normalizePhone($phone);
        if ($phone === '') return;

        AbandonedCheckout::where('phone', $phone)->update([
            'status' => 'converted',
            'order_id' => $orderId,
            'last_activity_at' => Carbon::now(),
        ]);
    }
}
