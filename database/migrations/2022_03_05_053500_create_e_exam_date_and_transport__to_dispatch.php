<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEExamDateAndTransportToDispatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('dispatch', 'e_exam_date')) {
            Schema::table('dispatch', function (Blueprint $table) {
                $table->date('e_exam_date')->after('exam_date')->comment('結束檢測日期')->nullable();
            });
        }
        if (!Schema::hasColumn('dispatch', 'transport')) {
            Schema::table('dispatch', function (Blueprint $table) {
                $table->integer('transport')->after('exam_place')->comment('交通工具')->nullable();
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
        // Schema::dropIfExists('e_exam_date_and_transport__to_dispatch');
    }
}
