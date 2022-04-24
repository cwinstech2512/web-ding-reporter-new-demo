<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumToReportUt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_ut', 'Others')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('Others', 100)->after('couplant')->comment('其它');
            }); 
        }
        if (!Schema::hasColumn('report_ut', 'reference_block')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('reference_block', 100)->after('Others')->comment('參考規塊');
            }); 
        }
        if (!Schema::hasColumn('report_ut', 'reference_serial_no')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('reference_serial_no', 100)->after('reference_block')->comment('參考規塊No');
            }); 
        }
        if (!Schema::hasColumn('report_ut', 'calibration_block')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('calibration_block', 100)->after('reference_serial_no')->comment('校正規塊');
            }); 
        }
        if (!Schema::hasColumn('report_ut', 'testing_method')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('testing_method', 100)->after('calibration_block')->comment('檢測方式');
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
        Schema::table('report_ut', function (Blueprint $table) {
            //
        });
    }
}
