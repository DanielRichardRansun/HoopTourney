<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('team_tournament', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('tournament_id')->nullable();
            $table->timestamps();

            // Jika ingin ada foreign key constraint, tetapi tetap nullable
            $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
            $table->foreign('tournament_id')->references('id')->on('tournaments')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('team_tournament');
    }
};
