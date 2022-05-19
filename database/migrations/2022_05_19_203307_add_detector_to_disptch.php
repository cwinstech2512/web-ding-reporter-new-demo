<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetectorToDisptch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('dispatch', 'inspector')) {
            Schema::table('dispatch', function (Blueprint $table) {
                $table->string('inspector', 25)->after('delegator')->comment('檢測人員')->nullable();
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
        Schema::table('disptch', function (Blueprint $table) {
            //
        });
    }
}
