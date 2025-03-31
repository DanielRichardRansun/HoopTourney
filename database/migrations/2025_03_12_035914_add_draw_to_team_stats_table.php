<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('team_stats', function (Blueprint $table) {
            $table->integer('draw')->nullable()->after('losses'); // Tambah kolom draw setelah losses
        });
    }

    public function down(): void
    {
        Schema::table('team_stats', function (Blueprint $table) {
            $table->dropColumn('draw'); // Hapus kolom jika rollback
        });
    }
};
