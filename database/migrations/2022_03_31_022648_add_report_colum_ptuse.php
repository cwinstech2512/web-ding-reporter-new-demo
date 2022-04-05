<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportColumPtuse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_pt', 'penetrant_application')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('penetrant_application', 50)->after('surface_temp')->comment('施加滲透劑');
            });
        }
        if (!Schema::hasColumn('report_pt', 'dwell_time')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('dwell_time', 50)->after('penetrant_application')->comment('滲透時間');
            });
        }
        if (!Schema::hasColumn('report_pt', 'surface_temperature')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('surface_temperature', 50)->after('dwell_time')->comment('檢測時表面溫度');
            });
        }
        if (!Schema::hasColumn('report_pt', 'removal')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('removal', 50)->after('surface_temperature')->comment('滲透劑去除');
            });
        }
        if (!Schema::hasColumn('report_pt', 'drying_time')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('drying_time', 50)->after('removal')->comment('乾燥時間');
            });
        }
        if (!Schema::hasColumn('report_pt', 'developing_time')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('developing_time', 50)->after('drying_time')->comment('顯像時間');
            });
        }
        if (!Schema::hasColumn('report_pt', 'post_clean')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('post_clean', 50)->after('developing_time')->comment('施加顯像劑');
            });
        }
        if (!Schema::hasColumn('report_pt', 'cleaner')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('cleaner', 50)->after('post_clean')->comment('清洗劑');
            });
        }
        if (!Schema::hasColumn('report_pt', 'penetrant')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('penetrant', 50)->after('cleaner')->comment('滲透劑');
            });
        }
        if (!Schema::hasColumn('report_pt', 'developer')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('developer', 50)->after('penetrant')->comment('顯像劑');
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
