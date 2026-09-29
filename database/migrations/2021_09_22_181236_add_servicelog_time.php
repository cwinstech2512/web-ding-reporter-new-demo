<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServicelogTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servicelog', function (Blueprint $table) {
            $table->timestamps();
            $table->dropColumn('conn_clientid');
            $table->dropColumn('disconn_clientid');
            $table->dropColumn('conn_userid');
            $table->dropColumn('disconn_userid');
            $table->dropColumn('conn_name');
            $table->bigInteger('userid')->unsigned();
            $table->dateTime('service_join_time');
            $table->dateTime('service_end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servicelog', function (Blueprint $table) {
            //
        });
    }
}
