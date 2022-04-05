<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportColumMtNormal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_mt', 'white_light_meter')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('white_light_meter', 50)->after('black_light_meter')->comment('白光照度計');
            });
        }
        if (!Schema::hasColumn('report_mt', 'surface_temperature')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('surface_temperature', 50)->after('surface_status')->comment('試件表面溫度');
            });
        }
        if (!Schema::hasColumn('report_mt', 'light_intensity')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('light_intensity', 50)->after('surface_temperature')->comment('試件表面亮度');
            });
        }
        if (!Schema::hasColumn('report_mt', 'lifting_test')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('lifting_test', 50)->after('light_intensity')->comment('舉力測試');
            });
        }
        if (!Schema::hasColumn('report_mt', 'magnetic_particles')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('magnetic_particles', 50)->after('magnetizing_methon')->comment('磁粒');
            });
        }
        if (!Schema::hasColumn('report_mt', 'magnetic_equipment')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('magnetic_equipment', 50)->after('magnetic_particles')->comment('磁化設備');
            });
        }
        if (!Schema::hasColumn('report_mt', 'magnetic_field_indicator')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('magnetic_field_indicator', 50)->after('magnetic_equipment')->comment('磁場指示計');
            });
        }
        if (!Schema::hasColumn('report_mt', 'yoke_probe_space')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('yoke_probe_space', 50)->after('magnetic_field_indicator')->comment('磁軛間距');
            });
        }
        if (!Schema::hasColumn('report_mt', 'yoke_calibration_date')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('yoke_calibration_date', 50)->after('yoke_probe_space')->comment('磁軛校正日期');
            });
        }
        if (!Schema::hasColumn('report_mt', 'particles_manufacturer')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('particles_manufacturer', 50)->after('yoke_calibration_date')->comment('磁粒製造廠商');
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
