<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportColumBasic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_it', 'customer')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->string('customer', 50)->after('dispatch_id')->comment('顧客/地址');
            });
        }
        if (!Schema::hasColumn('report_ut', 'customer')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('customer', 50)->after('dispatch_id')->comment('顧客/地址');
            });
        }
        if (!Schema::hasColumn('report_mt', 'customer')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('customer', 50)->after('dispatch_id')->comment('顧客/地址');
            });
        }
        if (!Schema::hasColumn('report_pt', 'customer')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('customer', 50)->after('dispatch_id')->comment('顧客/地址');
            });
        }
        if (!Schema::hasColumn('report_rt', 'customer')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('customer', 50)->after('dispatch_id')->comment('顧客/地址');
            });
        }

        if (!Schema::hasColumn('report_it', 'applicable_code')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->text('applicable_code')->after('job_no')->comment('檢測規範');
            });
        }
        if (!Schema::hasColumn('report_ut', 'applicable_code')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->text('applicable_code')->after('job_no')->comment('檢測規範');
            });
        }
        if (!Schema::hasColumn('report_mt', 'applicable_code')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->text('applicable_code')->after('job_no')->comment('檢測規範');
            });
        }
        if (!Schema::hasColumn('report_pt', 'applicable_code')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->text('applicable_code')->after('job_no')->comment('檢測規範');
            });
        }
        if (!Schema::hasColumn('report_rt', 'applicable_code')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->text('applicable_code')->after('job_no')->comment('檢測規範');
            });
        }

        if (!Schema::hasColumn('report_it', 'criteria')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->text('criteria')->after('applicable_code')->comment('接受標準');
            });
        }
        if (!Schema::hasColumn('report_ut', 'criteria')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->text('criteria')->after('applicable_code')->comment('接受標準');
            });
        }
        if (!Schema::hasColumn('report_mt', 'criteria')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->text('criteria')->after('applicable_code')->comment('接受標準');
            });
        }
        if (!Schema::hasColumn('report_pt', 'criteria')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->text('criteria')->after('applicable_code')->comment('接受標準');
            });
        }
        if (!Schema::hasColumn('report_rt', 'criteria')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->text('criteria')->after('applicable_code')->comment('接受標準');
            });
        }

        if (!Schema::hasColumn('report_it', 'procedure_no')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->text('procedure_no')->after('criteria')->comment('程序書編號');
            });
        }
        if (!Schema::hasColumn('report_ut', 'procedure_no')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->text('procedure_no')->after('criteria')->comment('程序書編號');
            });
        }
        if (!Schema::hasColumn('report_mt', 'procedure_no')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->text('procedure_no')->after('criteria')->comment('程序書編號');
            });
        }
        if (!Schema::hasColumn('report_pt', 'procedure_no')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->text('procedure_no')->after('criteria')->comment('程序書編號');
            });
        }
        if (!Schema::hasColumn('report_rt', 'procedure_no')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->text('procedure_no')->after('criteria')->comment('程序書編號');
            });
        }


        if (!Schema::hasColumn('report_it', 'region')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->string('region', 100)->after('procedure_no')->comment('區域/品名');
            });
        }
        if (!Schema::hasColumn('report_ut', 'region')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('region', 100)->after('procedure_no')->comment('區域/品名');
            });
        }
        if (!Schema::hasColumn('report_mt', 'region')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('region', 100)->after('procedure_no')->comment('區域/品名');
            });
        }
        if (!Schema::hasColumn('report_pt', 'region')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('region', 100)->after('procedure_no')->comment('區域/品名');
            });
        }
        if (!Schema::hasColumn('report_rt', 'region')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('region', 100)->after('procedure_no')->comment('區域/品名');
            });
        }


        if (!Schema::hasColumn('report_it', 'contract_no')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->string('contract_no', 100)->after('region')->comment('合約編號');
            });
        }
        if (!Schema::hasColumn('report_ut', 'contract_no')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('contract_no', 100)->after('region')->comment('合約編號');
            });
        }
        if (!Schema::hasColumn('report_mt', 'contract_no')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('contract_no', 100)->after('region')->comment('合約編號');
            });
        }
        if (!Schema::hasColumn('report_pt', 'contract_no')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('contract_no', 100)->after('region')->comment('合約編號');
            });
        }
        if (!Schema::hasColumn('report_rt', 'contract_no')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('contract_no', 100)->after('region')->comment('合約編號');
            });
        }


        if (!Schema::hasColumn('report_it', 'exam_method')) {
            Schema::table('report_it', function (Blueprint $table) {
                $table->string('exam_method', 100)->after('exam_date')->comment('檢測方法');
            });
        }
        if (!Schema::hasColumn('report_ut', 'exam_method')) {
            Schema::table('report_ut', function (Blueprint $table) {
                $table->string('exam_method', 100)->after('exam_date')->comment('檢測方法');
            });
        }
        if (!Schema::hasColumn('report_mt', 'exam_method')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('exam_method', 100)->after('exam_date')->comment('檢測方法');
            });
        }
        if (!Schema::hasColumn('report_pt', 'exam_method')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('exam_method', 100)->after('exam_date')->comment('檢測方法');
            });
        }
        if (!Schema::hasColumn('report_rt', 'exam_method')) {
            Schema::table('report_rt', function (Blueprint $table) {
                $table->string('exam_method', 100)->after('exam_date')->comment('檢測方法');
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
