<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['tournaments_id']); // Hapus foreign key jika ada
            $table->dropColumn('tournaments_id'); // Hapus kolom
        });
    }

    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedBigInteger('tournaments_id')->nullable()->after('manager');
            $table->foreign('tournaments_id')->references('id')->on('tournaments')->nullOnDelete();
        });
    }
};
