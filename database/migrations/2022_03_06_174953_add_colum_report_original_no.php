<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumReportOriginalNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_it', 'original_no')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->string('original_no', 25)->first()->comment('報告流水編號')->nullable();
            });
        }
        if (!Schema::hasColumn('report_ut', 'original_no')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('original_no', 25)->first()->comment('報告流水編號')->nullable();
            });
        }
        if (!Schema::hasColumn('report_mt', 'original_no')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('original_no', 25)->first()->comment('報告流水編號')->nullable();
            });
        }
        if (!Schema::hasColumn('report_pt', 'original_no')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('original_no', 25)->first()->comment('報告流水編號')->nullable();
            });
        }
        if (!Schema::hasColumn('report_rt', 'original_no')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('original_no', 25)->first()->comment('報告流水編號')->nullable();
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
