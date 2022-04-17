<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClomuntoreportMt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_mt', 'joint_type')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('joint_type', 50)->after('magnetizing_current')->comment('接頭形式');
            }); 
        }
        if (!Schema::hasColumn('report_mt', 'background_paint')) {
            Schema::table('report_mt', function (Blueprint $table) {
                $table->string('background_paint', 50)->after('joint_type')->comment('對比漆');
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
