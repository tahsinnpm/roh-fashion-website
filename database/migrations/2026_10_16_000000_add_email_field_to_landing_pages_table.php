<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailFieldToLandingPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('landing_pages', 'email_field')) {
            Schema::table('landing_pages', function (Blueprint $table) {
                $table->string('email_field')->default('hidden')->after('bg_color');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('landing_pages', 'email_field')) {
            Schema::table('landing_pages', function (Blueprint $table) {
                $table->dropColumn('email_field');
            });
        }
    }
}
