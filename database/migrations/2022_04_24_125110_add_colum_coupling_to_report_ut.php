<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumCouplingToReportUt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_ut', 'coupling')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('coupling', 100)->after('couplant')->comment('海基耦合劑');
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
