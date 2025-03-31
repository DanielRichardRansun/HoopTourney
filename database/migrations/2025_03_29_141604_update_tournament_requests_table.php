<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tournament_requests', function (Blueprint $table) {
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // siapa yang mengajukan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('tournament_requests', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['tournament_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['team_id', 'tournament_id', 'user_id', 'status']);
        });
    }
};
