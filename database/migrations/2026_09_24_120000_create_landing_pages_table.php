<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLandingPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('sub_title')->nullable();
            $table->string('theme_color')->default('#275b57');
            $table->string('hero_tag')->nullable();
            $table->longText('slider_images')->nullable();
            $table->string('video_url')->nullable();
            $table->decimal('regular_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('discount_text')->nullable();
            $table->string('stock_text')->nullable()->default('বর্তমান স্টক: সীমিত');
            $table->longText('features')->nullable();
            $table->string('overview_title')->nullable()->default('Product Full Overview');
            $table->text('overview_sub_title')->nullable();
            $table->string('overview_image')->nullable();
            $table->longText('overview_text')->nullable();
            $table->longText('overview_points')->nullable();
            $table->longText('about_cards')->nullable();
            $table->longText('reviews')->nullable();
            $table->longText('faqs')->nullable();
            $table->decimal('delivery_charge_dhaka', 10, 2)->default(70.00);
            $table->decimal('delivery_charge_outside', 10, 2)->default(120.00);
            $table->tinyInteger('status')->default(1)->comment('1=>Active, 0=>Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landing_pages');
    }
}
