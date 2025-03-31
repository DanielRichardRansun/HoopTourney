<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('players_id')->constrained('players')->onDelete('cascade');
            $table->foreignId('match_results_id')->constrained('match_results')->onDelete('cascade');
            $table->integer('per')->nullable();
            $table->integer('mp')->nullable();
            $table->integer('3p')->nullable();
            $table->integer('2p')->nullable();
            $table->integer('ast')->nullable();
            $table->integer('fg')->nullable();
            $table->integer('ft')->nullable();
            $table->integer('tov')->nullable();
            $table->integer('drb')->nullable();
            $table->integer('orb')->nullable();
            $table->integer('fga')->nullable();
            $table->integer('fta')->nullable();
            $table->integer('pf')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('player_stats');
    }
};
