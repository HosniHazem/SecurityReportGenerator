<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAuditPreviousauditsApTable extends Migration
{
    public function up()
    {
        Schema::table('audit_previousaudits_ap', function (Blueprint $table) {
            $table->unsignedBigInteger('ID_Projet')->nullable()->change();
            $table->foreign('ID_Projet')->references('id')->on('projects');
        });
    }

    public function down()
    {
        Schema::table('audit_previousaudits_ap', function (Blueprint $table) {
            $table->dropForeign(['ID_Projet']);
        });
    }
}
