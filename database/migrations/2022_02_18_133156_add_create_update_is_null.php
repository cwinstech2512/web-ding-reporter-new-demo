<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreateUpdateIsNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('report_it', function (Blueprint $table) {
            $table->string('created_user', 50)->nullable()->change();
            $table->string('updated_user', 50)->nullable()->change();
        });

        Schema::table('report_ut', function (Blueprint $table) {
            $table->string('created_user', 50)->nullable()->change();
            $table->string('updated_user', 50)->nullable()->change();
        });

        Schema::table('report_mt', function (Blueprint $table) {
            $table->string('created_user', 50)->nullable()->change();
            $table->string('updated_user', 50)->nullable()->change();
        });

        Schema::table('report_pt', function (Blueprint $table) {
            $table->string('created_user', 50)->nullable()->change();
            $table->string('updated_user', 50)->nullable()->change();
        });

        Schema::table('report_rt', function (Blueprint $table) {
            $table->string('created_user', 50)->nullable()->change();
            $table->string('updated_user', 50)->nullable()->change();
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
