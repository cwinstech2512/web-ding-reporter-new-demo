<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApplicableToDispatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('dispatch', 'report_version')) {
            Schema::table('dispatch', function (Blueprint $table) {
                $table->string('report_version', 25)->after('status')->comment('報告版本')->nullable();
            });
        }
        if (!Schema::hasColumn('dispatch', 'applicable_code')) {
            Schema::table('dispatch', function (Blueprint $table) {
                $table->text('applicable_code')->after('report_version')->comment('檢測規範')->nullable();
            });
        }
        if (!Schema::hasColumn('dispatch', 'criteria')) {
            Schema::table('dispatch', function (Blueprint $table) {
                $table->text('criteria')->after('applicable_code')->comment('接受標準')->nullable();
            });
        }
        if (!Schema::hasColumn('dispatch', 'procedure_no')) {
            Schema::table('dispatch', function (Blueprint $table) {
                $table->text('procedure_no')->after('criteria')->comment('程序書編號')->nullable();
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
        Schema::table('dispatch', function (Blueprint $table) {
            //
        });
    }
}
