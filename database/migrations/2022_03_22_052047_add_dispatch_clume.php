<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDispatchClume extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispatch', function (Blueprint $table) {
            $table->string('concat_name', 20)->after('customer')->comment('委託者聯絡人')->nullable();
            $table->char('concat_phone', 20)->after('concat_name')->comment('委託者電話')->nullable();
            $table->char('material', 20)->after('exam_place')->comment('材質')->nullable();
            $table->char('thickness', 20)->after('material')->comment('厚度')->nullable();
            $table->char('leng', 20)->after('thickness')->comment('數量（長度）')->nullable();
            $table->char('editor', 20)->after('leng')->comment('填寫人')->nullable();
            $table->char('special_film', 20)->after('editor')->comment('工作特殊需求(底片)')->nullable();
            $table->char('special_medium', 20)->after('special_film')->comment('工作特殊需求(藥水)')->nullable();
        });
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
