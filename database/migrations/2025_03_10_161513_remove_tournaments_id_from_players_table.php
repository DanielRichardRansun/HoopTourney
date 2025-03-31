<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('players', function (Blueprint $table) {
            // Hapus foreign key jika ada (gunakan raw query untuk menghindari error)
            DB::statement('ALTER TABLE players DROP FOREIGN KEY IF EXISTS players_tournaments_id_foreign');
            
            // Hapus kolom tournaments_id
            $table->dropColumn('tournaments_id');
        });
    }

    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->unsignedBigInteger('tournaments_id')->nullable();
            $table->foreign('tournaments_id')->references('id')->on('tournaments')->onDelete('cascade');
        });
    }
};
