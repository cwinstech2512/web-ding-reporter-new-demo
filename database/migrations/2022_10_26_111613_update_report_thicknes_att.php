<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReportThicknesAtt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('report_it', 'thicknes')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->string('thicknes', 50)->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_ut', 'thicknes')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('thicknes', 50)->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_mt', 'thicknes')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('thicknes', 50)->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_pt', 'thicknes')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('thicknes', 50)->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_rt', 'thicknes')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('thicknes', 50)->change()->nullable();
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
