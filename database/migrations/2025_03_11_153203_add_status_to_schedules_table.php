<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->enum('status', ['Scheduled', 'Completed', 'Postponed', 'Cancelled'])
                ->default('Scheduled')
                ->after('location'); // Menempatkan kolom setelah 'location'
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
