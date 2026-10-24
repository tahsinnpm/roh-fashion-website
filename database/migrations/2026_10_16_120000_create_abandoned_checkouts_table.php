<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_checkouts', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->unique();
            $table->string('name', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->text('address')->nullable();
            $table->string('source', 32);
            $table->string('landing_slug', 191)->nullable();
            $table->json('cart_snapshot')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_checkouts');
    }
};
