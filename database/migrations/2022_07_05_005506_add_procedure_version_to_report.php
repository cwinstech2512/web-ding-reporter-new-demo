<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcedureVersionToReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_ut', 'procedure_version')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('procedure_version', 50)->after('procedure_no')->nullable()->comment('版次');
            });
        }
        if (!Schema::hasColumn('report_mt', 'procedure_version')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('procedure_version', 50)->after('procedure_no')->nullable()->comment('版次');
            });
        }
        if (!Schema::hasColumn('report_pt', 'procedure_version')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('procedure_version', 50)->after('procedure_no')->nullable()->comment('版次');
            });
        }
        if (!Schema::hasColumn('report_rt', 'procedure_version')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('procedure_version', 50)->after('procedure_no')->nullable()->comment('版次');
            });
        }
        if (!Schema::hasColumn('report_vt', 'procedure_version')) {
            Schema::table('report_vt', function (Blueprint $table) {
                $table->string('procedure_version', 50)->after('procedure_no')->nullable()->comment('版次');
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
        Schema::table('report', function (Blueprint $table) {
            //
        });
    }
}
