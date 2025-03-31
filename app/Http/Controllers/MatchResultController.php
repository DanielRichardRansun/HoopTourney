<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Tournament;
use App\Models\MatchResult;
use App\Models\TeamStat;
use App\Models\Player;
use App\Models\PlayerStat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchResultController extends Controller
{

    public function create($id_tournament, $id_schedule)
    {
        // Ambil data tournament dan schedule berdasarkan ID
        $tournament = Tournament::findOrFail($id_tournament);
        $schedule = Schedule::findOrFail($id_schedule);

        // Ambil tim berdasarkan schedule
        $team1 = $schedule->team1;
        $team2 = $schedule->team2;

        // Validasi jika tim tidak ditemukan
        if (!$team1 || !$team2) {
            return redirect()->back()->with('error', 'Tim tidak ditemukan dalam jadwal ini.');
        }

        // Ambil pemain dari kedua tim
        $players1 = Player::where('teams_id', $team1->id)->get();
        $players2 = Player::where('teams_id', $team2->id)->get();

        return view('match_results.create', compact('tournament', 'schedule', 'team1', 'team2', 'players1', 'players2'));
    }

    public function store(Request $request, $id_tournament, $id_schedule)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'team1_score' => 'required|integer|min:0',
                'team2_score' => 'required|integer|min:0',
            ]);

            $schedule = Schedule::findOrFail($id_schedule);

            // Pastikan selalu ada pemenang dan pecundang
            $winningTeamId = $request->team1_score > $request->team2_score
                ? $schedule->team1_id
                : $schedule->team2_id;

            $losingTeamId = $request->team1_score < $request->team2_score
                ? $schedule->team1_id
                : $schedule->team2_id;

            // Simpan hasil pertandingan
            $matchResult = MatchResult::create([
                'team1_score' => $request->team1_score,
                'team2_score' => $request->team2_score,
                'winning_team_id' => $winningTeamId,
                'losing_team_id' => $losingTeamId,
                'schedules_id' => $schedule->id,
            ]);

            $schedule->update(['status' => 'Completed']);

            // Update statistik tim - PAKAI CARA INI UNTUK PASTIKAN DATA MASUK
            DB::table('team_stats')
                ->updateOrInsert(
                    ['teams_id' => $winningTeamId, 'tournaments_id' => $id_tournament],
                    ['wins' => DB::raw('COALESCE(wins, 0) + 1')]
                );

            DB::table('team_stats')
                ->updateOrInsert(
                    ['teams_id' => $losingTeamId, 'tournaments_id' => $id_tournament],
                    ['losses' => DB::raw('COALESCE(losses, 0) + 1')]
                );

            // Simpan statistik pemain
            foreach ($request->player_stats as $playerId => $stats) {
                PlayerStat::create([
                    'players_id' => $playerId,
                    'match_results_id' => $matchResult->id,
                    'per' => $stats['per'] ?? 0,
                    'point' => $stats['point'] ?? 0,
                    'fgm' => $stats['fgm'] ?? 0,
                    'fga' => $stats['fga'] ?? 0,
                    'fta' => $stats['fta'] ?? 0,
                    'ftm' => $stats['ftm'] ?? 0,
                    'orb' => $stats['orb'] ?? 0,
                    'drb' => $stats['drb'] ?? 0,
                    'stl' => $stats['stl'] ?? 0,
                    'ast' => $stats['ast'] ?? 0,
                    'blk' => $stats['blk'] ?? 0,
                    'pf' => $stats['pf'] ?? 0,
                    'to' => $stats['to'] ?? 0,
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard.jadwal', $id_tournament)
                ->with('success', 'Hasil pertandingan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function edit($id_tournament, $id_schedule)
    {
        // Ambil data tournament dan schedule berdasarkan ID
        $tournament = Tournament::findOrFail($id_tournament);
        $schedule = Schedule::findOrFail($id_schedule);

        // Ambil hasil pertandingan yang sudah ada
        $matchResult = $schedule->matchResult;

        // Jika hasil pertandingan tidak ditemukan, kembalikan error
        if (!$matchResult) {
            return redirect()->back()->with('error', 'Hasil pertandingan tidak ditemukan.');
        }

        // Ambil tim berdasarkan schedule
        $team1 = $schedule->team1;
        $team2 = $schedule->team2;

        // Ambil pemain dari kedua tim
        $players1 = Player::where('teams_id', $team1->id)->get();
        $players2 = Player::where('teams_id', $team2->id)->get();

        // Ambil statistik pemain yang sudah ada
        $playerStats = PlayerStat::where('match_results_id', $matchResult->id)->get()->keyBy('players_id');

        return view('match_results.edit', compact('tournament', 'schedule', 'team1', 'team2', 'players1', 'players2', 'matchResult', 'playerStats'));
    }

    public function update(Request $request, $id_tournament, $id_schedule)
    {
        DB::beginTransaction();
        try {
            // Validasi input
            $request->validate([
                'team1_score' => 'required|integer|min:0',
                'team2_score' => 'required|integer|min:0',
            ]);

            // Ambil data schedule dan match result
            $schedule = Schedule::findOrFail($id_schedule);
            $matchResult = $schedule->matchResult;

            if (!$matchResult) {
                return redirect()->back()->with('error', 'Hasil pertandingan tidak ditemukan.');
            }

            // Tentukan pemenang dan pecundang sebelumnya
            $previousWinner = $matchResult->winning_team_id;
            $previousLoser = $matchResult->losing_team_id;

            // Tentukan pemenang dan pecundang baru
            $newWinner = $request->team1_score > $request->team2_score
                ? $schedule->team1_id
                : $schedule->team2_id;

            $newLoser = $request->team1_score < $request->team2_score
                ? $schedule->team1_id
                : $schedule->team2_id;

            // Update hasil pertandingan
            $matchResult->update([
                'team1_score' => $request->team1_score,
                'team2_score' => $request->team2_score,
                'winning_team_id' => $newWinner,
                'losing_team_id' => $newLoser,
            ]);

            // Handle perubahan statistik tim
            DB::beginTransaction();

            try {
                // 1. Reset statistik tim dari pertandingan sebelumnya
                // Untuk pemenang sebelumnya: kurangi 1 win
                DB::table('team_stats')
                    ->where('teams_id', $previousWinner)
                    ->where('tournaments_id', $id_tournament)
                    ->update([
                        'wins' => DB::raw('GREATEST(wins - 1, 0)')
                    ]);

                // Untuk pecundang sebelumnya: kurangi 1 loss
                DB::table('team_stats')
                    ->where('teams_id', $previousLoser)
                    ->where('tournaments_id', $id_tournament)
                    ->update([
                        'losses' => DB::raw('GREATEST(losses - 1, 0)')
                    ]);

                // 2. Update statistik untuk pertandingan baru
                // Untuk pemenang baru: tambahkan 1 win
                DB::table('team_stats')
                    ->updateOrInsert(
                        ['teams_id' => $newWinner, 'tournaments_id' => $id_tournament],
                        ['wins' => DB::raw('COALESCE(wins, 0) + 1'), 'losses' => DB::raw('COALESCE(losses, 0)')]
                    );

                // Untuk pecundang baru: tambahkan 1 loss
                DB::table('team_stats')
                    ->updateOrInsert(
                        ['teams_id' => $newLoser, 'tournaments_id' => $id_tournament],
                        ['losses' => DB::raw('COALESCE(losses, 0) + 1'), 'wins' => DB::raw('COALESCE(wins, 0)')]
                    );

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            // Update statistik pemain
            foreach ($request->player_stats as $playerId => $stats) {
                PlayerStat::updateOrCreate(
                    ['players_id' => $playerId, 'match_results_id' => $matchResult->id],
                    [
                        'per' => $stats['per'] ?? 0,
                        'point' => $stats['point'] ?? 0,
                        'fgm' => $stats['fgm'] ?? 0,
                        'fga' => $stats['fga'] ?? 0,
                        'fta' => $stats['fta'] ?? 0,
                        'ftm' => $stats['ftm'] ?? 0,
                        'orb' => $stats['orb'] ?? 0,
                        'drb' => $stats['drb'] ?? 0,
                        'stl' => $stats['stl'] ?? 0,
                        'ast' => $stats['ast'] ?? 0,
                        'blk' => $stats['blk'] ?? 0,
                        'pf' => $stats['pf'] ?? 0,
                        'to' => $stats['to'] ?? 0,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('dashboard.jadwal', $id_tournament)
                ->with('success', 'Hasil pertandingan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }
}
