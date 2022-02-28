<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRadiographicTechType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('report_it', 'radiographic_tech')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->string('radiographic_tech', 50)->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_ut', 'radiographic_tech')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('radiographic_tech', 50)->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_mt', 'radiographic_tech')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('radiographic_tech', 50)->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_pt', 'radiographic_tech')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('radiographic_tech', 50)->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_rt', 'radiographic_tech')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('radiographic_tech', 50)->change()->nullable();
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
