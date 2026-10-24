<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifySettingsValueToLongtext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop index if exists, then modify value column to LONGTEXT
        try {
            DB::statement('ALTER TABLE settings DROP INDEX value');
        } catch (\Throwable $e) {
            // Index may already be dropped
        }

        DB::statement('ALTER TABLE settings MODIFY value LONGTEXT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE settings MODIFY value VARCHAR(255) NULL');
    }
}
