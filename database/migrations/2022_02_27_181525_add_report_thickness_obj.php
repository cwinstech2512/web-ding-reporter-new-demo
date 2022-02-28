<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportThicknessObj extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_it', 'thickness_objs')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->text('thickness_objs')->after('items')->nullable();
            });
        }
        if (!Schema::hasColumn('report_ut', 'thickness_objs')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->text('thickness_objs')->after('items')->nullable();
            });
        }
        if (!Schema::hasColumn('report_mt', 'thickness_objs')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->text('thickness_objs')->after('items')->nullable();
            });
        }
        if (!Schema::hasColumn('report_pt', 'thickness_objs')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->text('thickness_objs')->after('items')->nullable();
            });
        }
        if (!Schema::hasColumn('report_rt', 'thickness_objs')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->text('thickness_objs')->after('items')->nullable();
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
