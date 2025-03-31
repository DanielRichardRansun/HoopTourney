<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePlayerStatsTable extends Migration
{
    /**
     * Jalankan migrasi untuk mengubah struktur tabel.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_stats', function (Blueprint $table) {
            // Hapus kolom yang tidak diperlukan
            $table->dropColumn([
                'per',
                'point',
                'fgm',
                '2p',
                'ast',
                'fg',
                'ft',
                'tov',
                'drb',
                'orb',
                'fga',
                'fta',
                'pf'
            ]);

            // Tambahkan kolom baru dengan huruf kecil
            $table->float('per')->after('match_results_id')->nullable();
            $table->float('point')->after('per')->nullable();
            $table->float('fgm')->after('point')->nullable();
            $table->float('fga')->after('fgm')->nullable();
            $table->float('fta')->after('fga')->nullable();
            $table->float('ftm')->after('fta')->nullable();
            $table->float('orb')->after('ftm')->nullable();
            $table->float('drb')->after('orb')->nullable();
            $table->float('stl')->after('drb')->nullable();
            $table->float('ast')->after('stl')->nullable();
            $table->float('blk')->after('ast')->nullable();
            $table->float('pf')->after('blk')->nullable();
            $table->float('to')->after('pf')->nullable();
        });
    }

    /**
     * Batalkan perubahan jika diperlukan.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('player_stats', function (Blueprint $table) {
            // Kembalikan kolom yang telah dihapus
            $table->dropColumn([
                'per',
                'point',
                'fgm',
                'fga',
                'fta',
                'ftm',
                'orb',
                'drb',
                'stl',
                'ast',
                'blk',
                'pf',
                'to'
            ]);

            // Tambahkan kembali kolom lama yang dihapus sebelumnya
            $table->float('per')->after('match_results_id')->nullable();
            $table->float('point')->after('per')->nullable();
            $table->float('fgm')->after('point')->nullable();
            $table->float('2p')->after('fgm')->nullable();
            $table->float('ast')->after('2p')->nullable();
            $table->float('fg')->after('ast')->nullable();
            $table->float('ft')->after('fg')->nullable();
            $table->float('tov')->after('ft')->nullable();
            $table->float('drb')->after('tov')->nullable();
            $table->float('orb')->after('drb')->nullable();
            $table->float('fga')->after('orb')->nullable();
            $table->float('fta')->after('fga')->nullable();
            $table->float('pf')->after('fta')->nullable();
        });
    }
}
