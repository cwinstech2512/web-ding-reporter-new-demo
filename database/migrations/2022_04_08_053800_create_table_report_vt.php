<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableReportVt extends Migration
{
    /**
     * Run the migrations.
     * @table report_it
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_vt', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('original_no', 25)->nullable()->default(null)->comment('報告流水編號');
            $table->bigIncrements('id');
            $table->bigInteger('dispatch_id');
            $table->string('customer', 50)->comment('顧客/地址');
            $table->string('job_name', 100)->comment('工程名稱')->nullable()->default(null);
            $table->string('job_no', 50)->comment('工程案號')->nullable()->default(null);
            $table->text('applicable_code')->comment('檢測規範');
            $table->text('criteria')->comment('接受標準');
            $table->text('procedure_no')->comment('程序書編號');
            $table->string('region', 100)->comment('區域/品名');
            $table->string('contract_no', 100)->comment('合約編號');
            $table->date('exam_date')->comment('檢測日期');
            $table->char('exam_time', 30)->comment('檢測時機');
            $table->string('exam_method', 100)->comment('檢測方法');
            $table->string('exam_place', 50)->comment('檢測地點');
            $table->tinyInteger('exam_type')->nullable()->default(null)->comment('檢測類別');
            $table->char('surface_status', 30)->nullable()->default(null)->comment('表面狀況');
            $table->char('material', 10)->comment('材質');
            $table->char('equipement', 20)->comment('設 備');
            $table->char('testing_method', 30)->comment('檢 測 方 式');
            $table->char('thickness', 20)->comment('包 覆 厚 度');
            $table->string('lumin_meter', 100)->comment('照度計');
            $table->string('weld_gage', 100)->comment('銲道規');
            $table->string('Others', 100)->comment('其它');
            $table->char('light_intensity', 20)->comment('檢測區照度');
            $table->char('joint_type', 20)->comment('接頭形式');
            $table->mediumText('items')->nullable()->default(null);
            $table->text('thickness_objs')->nullable()->default(null);
            $table->char('obsert', 7)->nullable()->default(null)->comment('監工');
            $table->string('report_version', 25)->nullable()->default(null)->comment('報告版本');
            $table->tinyInteger('status')->comment('	0: 派單建立, 1:報告建立, 2:報告輸入中, ３: 審核中, ４: 審核完成, 5: 刪除');
            $table->string('created_user', 50)->nullable()->default(null);
            $table->string('updated_user', 50)->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_report_vt');
    }
}
