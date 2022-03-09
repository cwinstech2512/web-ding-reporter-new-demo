<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumReportReportVersion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_it', 'report_version')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->string('report_version', 25)->after('obsert')->comment('報告版本')->nullable();
            });
        }
        if (!Schema::hasColumn('report_ut', 'report_version')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('report_version', 25)->after('obsert')->comment('報告版本')->nullable();
            });
        }
        if (!Schema::hasColumn('report_mt', 'report_version')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('report_version', 25)->after('obsert')->comment('報告版本')->nullable();
            });
        }
        if (!Schema::hasColumn('report_pt', 'report_version')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('report_version', 25)->after('obsert')->comment('報告版本')->nullable();
            });
        }
        if (!Schema::hasColumn('report_rt', 'report_version')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('report_version', 25)->after('obsert')->comment('報告版本')->nullable();
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
