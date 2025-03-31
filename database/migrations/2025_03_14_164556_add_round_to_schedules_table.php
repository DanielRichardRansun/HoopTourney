<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->enum('round', [
                'penyisihan1',
                'penyisihan2',
                'penyisihan3',
                'quarterfinal',
                'semifinal',
                'final'
            ])->after('status')->default('penyisihan1');
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('round');
        });
    }
};
