<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportUtNewVersion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_ut', 'brand_type_ser_no')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('brand_type_ser_no', 50)->after('exam_time')->nullable()->comment('廠牌型號/序號');
            });
        }
        if (!Schema::hasColumn('report_ut', 'cali_date')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('cali_date', 50)->after('brand_type_ser_no')->nullable()->comment('儀器校準日期');
            });
        }
        if (!Schema::hasColumn('report_ut', 'scanning_sensitivity')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('scanning_sensitivity', 50)->after('cali_date')->nullable()->comment('檢測靈敏度');
            });
        }
        if (!Schema::hasColumn('report_ut', 'probe_no_ser_no')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('probe_no_ser_no', 50)->after('scanning_sensitivity')->nullable()->comment('換能器編號/序號');
            });
        }
        if (!Schema::hasColumn('report_ut', 'probe_size')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('probe_size', 50)->after('probe_no_ser_no')->nullable()->comment('換能器尺寸');
            });
        }
        if (!Schema::hasColumn('report_ut', 'incident_angle')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('incident_angle', 50)->after('probe_size')->nullable()->comment('換能器折射角');
            });
        }
        if (!Schema::hasColumn('report_ut', 'probe_frequency')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('probe_frequency', 50)->after('incident_angle')->nullable()->comment('換能器頻率');
            });
        }
        if (!Schema::hasColumn('report_ut', 'calibration_check_before')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('calibration_check_before', 50)->after('probe_frequency')->nullable()->comment('系統查核-檢測前');
            });
        }
        if (!Schema::hasColumn('report_ut', 'calibration_check_after')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('calibration_check_after', 50)->after('calibration_check_before')->nullable()->comment('系統查核-檢測後');
            });
        }
        if (!Schema::hasColumn('report_ut', 'probe_nb_ab')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('probe_nb_ab', 50)->after('calibration_check_after')->nullable()->comment('換能器直束/斜束 ');
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
