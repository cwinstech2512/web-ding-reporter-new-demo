<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeExposureTimeToChar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('report_it', 'exposure_time')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->string('exposure_time', 50)->change()->comment('曝光時間')->nullable();
            });
        }
        if (Schema::hasColumn('report_ut', 'exposure_time')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('exposure_time', 50)->change()->comment('曝光時間')->nullable();
            });
        }
        if (Schema::hasColumn('report_mt', 'exposure_time')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('exposure_time', 50)->change()->comment('曝光時間')->nullable();
            });
        }
        if (Schema::hasColumn('report_pt', 'exposure_time')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('exposure_time', 50)->change()->comment('曝光時間')->nullable();
            });
        }
        if (Schema::hasColumn('report_rt', 'exposure_time')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('exposure_time', 50)->change()->comment('曝光時間')->nullable();
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
    }
}
