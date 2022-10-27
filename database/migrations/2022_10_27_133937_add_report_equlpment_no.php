<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportEqulpmentNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_ut', 'equlpment_no')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('equlpment_no', 50)->after('contract_no')->nullable()->comment('設備編號');
            });
        }
        if (!Schema::hasColumn('report_mt', 'equlpment_no')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('equlpment_no', 50)->after('contract_no')->nullable()->comment('設備編號');
            });
        }
        if (!Schema::hasColumn('report_pt', 'equlpment_no')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('equlpment_no', 50)->after('contract_no')->nullable()->comment('設備編號');
            });
        }
        if (!Schema::hasColumn('report_rt', 'equlpment_no')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('equlpment_no', 50)->after('contract_no')->nullable()->comment('設備編號');
            });
        }
        if (!Schema::hasColumn('report_vt', 'equlpment_no')) {
            Schema::table('report_vt', function (Blueprint $table) {
                $table->string('equlpment_no', 50)->after('contract_no')->nullable()->comment('設備編號');
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
