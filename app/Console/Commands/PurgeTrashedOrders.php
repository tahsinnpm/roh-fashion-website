<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ReturnRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeTrashedOrders extends Command
{
    protected $signature = 'orders:purge-trash';
    protected $description = 'Permanently remove orders that have been in trash for 30 days';

    public function handle(): int
    {
        $cutoff = now()->subDays(30);
        $purged = 0;

        Order::onlyTrashed()->where('deleted_at', '<=', $cutoff)->orderBy('id')->chunkById(100, function ($orders) use (&$purged) {
            foreach ($orders as $order) {
                DB::transaction(function () use ($order, &$purged) {
                    $trashedOrder = Order::onlyTrashed()->lockForUpdate()->find($order->id);
                    if (!$trashedOrder || $trashedOrder->deleted_at->gt(now()->subDays(30))) return;

                    OrderDetail::where('order_id', $trashedOrder->id)->delete();
                    ReturnRequest::where('order_id', $trashedOrder->id)->delete();
                    $trashedOrder->forceDelete();
                    $purged++;
                });
            }
        });

        $this->info("Permanently removed {$purged} order(s).");
        return self::SUCCESS;
    }
}
