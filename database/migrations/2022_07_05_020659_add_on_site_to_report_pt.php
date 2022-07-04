<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnSiteToReportPt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('report_pt', 'on_site')) {
            Schema::table('report_pt', function (Blueprint $table) {
                $table->string('on_site', 50)->after('material')->nullable()->comment('現場、預置');
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
        Schema::table('report_pt', function (Blueprint $table) {
            //
        });
    }
}
