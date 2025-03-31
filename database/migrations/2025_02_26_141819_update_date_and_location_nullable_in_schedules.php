<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDateAndLocationNullableInSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dateTime('date')->nullable()->change(); // Mengubah kolom date agar bisa NULL
            $table->string('location')->nullable()->change(); // Mengubah kolom location agar bisa NULL
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dateTime('date')->nullable(false)->change(); // Menyembunyikan null di kolom date
            $table->string('location')->nullable(false)->change(); // Menyembunyikan null di kolom location
        });
    }
}
