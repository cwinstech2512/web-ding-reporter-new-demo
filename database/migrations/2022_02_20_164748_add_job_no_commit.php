<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobNoCommit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasColumn('report_it', 'job_no')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->string('job_no', 50)->after('job_name')->comment("工單編號")->nullable();
            });
        }
        if (!Schema::hasColumn('report_ut', 'job_no')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('job_no', 50)->after('job_name')->comment("工單編號")->nullable();
            });
        }
        if (!Schema::hasColumn('report_mt', 'job_no')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('job_no', 50)->after('job_name')->comment("工單編號")->nullable();
            });
        }
        if (!Schema::hasColumn('report_pt', 'job_no')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('job_no', 50)->after('job_name')->comment("工單編號")->nullable();
            });
        }
        if (!Schema::hasColumn('report_rt', 'job_no')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('job_no', 50)->after('job_name')->comment("工單編號")->nullable();
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
