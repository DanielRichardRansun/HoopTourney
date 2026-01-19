<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quarter_results', function (Blueprint $table) {
            $table->timestamps(); // Ini akan menambahkan created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quarter_results', function (Blueprint $table) {
            $table->dropTimestamps(); // Ini akan menghapus created_at dan updated_at
        });
    }
};
