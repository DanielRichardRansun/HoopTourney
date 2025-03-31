<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTournamentsIdToPlayersTable extends Migration
{
    public function up()
    {
        Schema::table('players', function (Blueprint $table) {
            if (!Schema::hasColumn('players', 'tournaments_id')) {
                $table->unsignedBigInteger('tournaments_id')->after('id');
                $table->foreign('tournaments_id')
                    ->references('id')
                    ->on('tournaments')
                    ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            if (Schema::hasColumn('players', 'tournaments_id')) {
                $table->dropForeign(['tournaments_id']);
                $table->dropColumn('tournaments_id');
            }
        });
    }
}
