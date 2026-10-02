<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorsAndExtraToLandingPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->string('btn_color')->nullable()->default('#E0B04A')->after('theme_color');
            $table->string('btn_text_color')->nullable()->default('#2B1C06')->after('btn_color');
            $table->string('bg_color')->nullable()->default('#1F0A0F')->after('btn_text_color');
            $table->string('top_notice')->nullable()->default('✦ ক্যাশ অন ডেলিভারি — সারাদেশে পণ্য হাতে পেয়ে, খুলে দেখে তারপর টাকা দিন')->after('hero_tag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn(['btn_color', 'btn_text_color', 'bg_color', 'top_notice']);
        });
    }
}
