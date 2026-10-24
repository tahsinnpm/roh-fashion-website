<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->json('review_images')->nullable()->after('review_image');
        });

        DB::table('landing_pages')->whereNotNull('review_image')->orderBy('id')->each(function ($landingPage) {
            DB::table('landing_pages')->where('id', $landingPage->id)->update([
                'review_images' => json_encode([$landingPage->review_image]),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn('review_images');
        });
    }
};
