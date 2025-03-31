<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('team_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('wins');
            $table->integer('losses');
            $table->integer('points');
            $table->foreignId('teams_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('tournaments_id')->constrained('tournaments')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('team_stats');
    }
};
