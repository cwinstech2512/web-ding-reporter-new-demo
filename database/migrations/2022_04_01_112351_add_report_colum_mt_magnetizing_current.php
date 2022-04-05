<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportColumMtMagnetizingCurrent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_mt', 'magnetizing_current')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('magnetizing_current', 50)->after('magnetic_particles')->comment('磁化電流');
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
        //
    }
}
