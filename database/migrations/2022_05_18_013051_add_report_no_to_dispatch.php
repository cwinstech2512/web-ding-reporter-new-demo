<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportNoToDispatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('dispatch', 'original_no')) {
            Schema::table('dispatch', function (Blueprint $table) {
                $table->string('original_no', 25)->after('serial_no')->comment('報告流水編號')->nullable();
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
        Schema::table('dispatch', function (Blueprint $table) {
            //
        });
    }
}
