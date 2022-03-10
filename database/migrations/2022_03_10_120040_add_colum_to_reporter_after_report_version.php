<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumToReporterAfterReportVersion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_it', 'status')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->tinyInteger('status')->after('report_version')->comment('	0: 派單建立, 1:報告建立, 2:報告輸入中, ３: 審核中, ４: 審核完成, 5: 刪除');
            });
        }
        if (!Schema::hasColumn('report_ut', 'status')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->tinyInteger('status')->after('report_version')->comment('	0: 派單建立, 1:報告建立, 2:報告輸入中, ３: 審核中, ４: 審核完成, 5: 刪除');
            });
        }
        if (!Schema::hasColumn('report_mt', 'status')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->tinyInteger('status')->after('report_version')->comment('	0: 派單建立, 1:報告建立, 2:報告輸入中, ３: 審核中, ４: 審核完成, 5: 刪除');
            });
        }
        if (!Schema::hasColumn('report_pt', 'status')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->tinyInteger('status')->after('report_version')->comment('	0: 派單建立, 1:報告建立, 2:報告輸入中, ３: 審核中, ４: 審核完成, 5: 刪除');
            });
        }
        if (!Schema::hasColumn('report_rt', 'status')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->tinyInteger('status')->after('report_version')->comment('	0: 派單建立, 1:報告建立, 2:報告輸入中, ３: 審核中, ４: 審核完成, 5: 刪除');
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
