<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReportItemToMeditext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('report_it', 'items')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->mediumText('items')->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_ut', 'items')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->mediumText('items')->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_mt', 'items')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->mediumText('items')->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_pt', 'items')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->mediumText('items')->change()->nullable();
            });
        }
        if (Schema::hasColumn('report_rt', 'items')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->mediumText('items')->change()->nullable();
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
