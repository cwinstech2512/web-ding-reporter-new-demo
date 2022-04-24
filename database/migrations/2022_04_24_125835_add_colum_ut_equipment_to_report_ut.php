<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumUtEquipmentToReportUt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_ut', 'ut_equipment')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('ut_equipment', 100)->after('couplant')->comment('探傷儀');
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
        Schema::table('report_ut', function (Blueprint $table) {
            //
        });
    }
}
