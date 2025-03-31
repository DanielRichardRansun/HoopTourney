<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->string('organizer', 45);
            $table->string('description', 250)->nullable();
            $table->string('start_date', 45);
            $table->string('end_date', 45);
            $table->enum('status', ['upcoming', 'ongoing', 'completed']);
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tournaments');
    }
};
