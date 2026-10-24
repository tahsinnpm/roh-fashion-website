<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes();
            $table->timestamp('stock_restored_at')->nullable();
        });

        // Legacy cancelled orders already had inventory returned by the existing flow.
        DB::table('orders')->where('delivery_status', 'cancelled')->whereNull('stock_restored_at')
            ->update(['stock_restored_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('stock_restored_at');
        });
    }
};
