<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDelegatorToDispatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('dispatch', 'delegator')) {
            Schema::table('dispatch', function (Blueprint $table) {
                $table->string('delegator', 100)->after('concat_name')->comment('委託者');
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
