<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('team1_id')->nullable()->change();
            $table->unsignedBigInteger('team2_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('team1_id')->nullable(false)->change();
            $table->unsignedBigInteger('team2_id')->nullable(false)->change();
        });
    }
};
