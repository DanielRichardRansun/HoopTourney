<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Tournament;
use App\Models\MatchResult;
use App\Models\TeamStat;
use App\Models\QuarterResult;
use App\Models\Player;
use App\Models\PlayerStat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Collection;

class MatchResultController extends Controller
{
    public function show($id_tournament, $id_schedule)
    {
        $tournament = Tournament::findOrFail($id_tournament);

        $schedule = Schedule::with(['team1.players', 'team2.players', 'matchResult'])->findOrFail($id_schedule);
        $matchResult = $schedule->matchResult;

        $quarterResults = QuarterResult::where('match_results_id', $matchResult->id)->get();

        $playerStatsPerQuarter = PlayerStat::where('match_results_id', $matchResult->id)
            ->with('player.team')
            ->get()
            ->groupBy('quarter_number');

        return view('match_results.show', compact(
            'schedule',
            'matchResult',
            'playerStatsPerQuarter',
            'quarterResults',
            'tournament'
        ));
    }

    public function create($id_tournament, $id_schedule)
    {
        $tournament = Tournament::findOrFail($id_tournament);
        $schedule = Schedule::findOrFail($id_schedule);

        $team1 = $schedule->team1;
        $team2 = $schedule->team2;

        if (!$team1 || !$team2) {
            return redirect()->back()->with('error', 'Tim tidak ditemukan dalam jadwal ini.');
        }

        $players1 = Player::where('teams_id', $team1->id)->get();
        $players2 = Player::where('teams_id', $team2->id)->get();

        return view('match_results.create', compact('tournament', 'schedule', 'team1', 'team2', 'players1', 'players2'));
    }

    public function store(Request $request, $id_tournament, $id_schedule)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'quarter_scores.*.team1_score' => 'required|integer|min:0', // Validasi untuk setiap kuarter
                'quarter_scores.*.team2_score' => 'required|integer|min:0',
                'player_stats.*.*.point' => 'nullable|integer|min:0', // Validasi statistik pemain
                'player_stats.*.*.fgm' => 'nullable|integer|min:0',
                'player_stats.*.*.fga' => 'nullable|integer|min:0',
                'player_stats.*.*.fta' => 'nullable|integer|min:0',
                'player_stats.*.*.ftm' => 'nullable|integer|min:0',
                'player_stats.*.*.orb' => 'nullable|integer|min:0',
                'player_stats.*.*.drb' => 'nullable|integer|min:0',
                'player_stats.*.*.stl' => 'nullable|integer|min:0',
                'player_stats.*.*.ast' => 'nullable|integer|min:0',
                'player_stats.*.*.blk' => 'nullable|integer|min:0',
                'player_stats.*.*.pf' => 'nullable|integer|min:0',
                'player_stats.*.*.to' => 'nullable|integer|min:0',
            ]);

            $schedule = Schedule::findOrFail($id_schedule);

            // Hitung skor total dari semua kuarter
            $totalTeam1Score = 0;
            $totalTeam2Score = 0;
            foreach ($request->quarter_scores as $quarterData) {
                $totalTeam1Score += $quarterData['team1_score'];
                $totalTeam2Score += $quarterData['team2_score'];
            }

            $winningTeamId = $totalTeam1Score > $totalTeam2Score
                ? $schedule->team1_id
                : $schedule->team2_id;

            $losingTeamId = $totalTeam1Score < $totalTeam2Score
                ? $schedule->team1_id
                : $schedule->team2_id;

            // Buat entri MatchResult utama
            $matchResult = MatchResult::create([
                'team1_score' => $totalTeam1Score,
                'team2_score' => $totalTeam2Score,
                'winning_team_id' => $winningTeamId,
                'losing_team_id' => $losingTeamId,
                'schedules_id' => $schedule->id,
            ]);

            // Perbarui status jadwal menjadi 'Completed'
            $schedule->update(['status' => 'Completed']);

            // Simpan skor per kuarter
            foreach ($request->quarter_scores as $quarterNumber => $quarterData) {
                QuarterResult::create([
                    'match_results_id' => $matchResult->id,
                    'quarter_number' => $quarterNumber,
                    'team1_score' => $quarterData['team1_score'],
                    'team2_score' => $quarterData['team2_score'],
                ]);
            }

            // Perbarui statistik tim
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

            // Simpan statistik pemain per kuarter
            foreach ($request->player_stats as $playerId => $quarters) {
                foreach ($quarters as $quarterNumber => $stats) {
                    $per = $this->calculatePER($stats);

                    PlayerStat::create([
                        'players_id' => $playerId,
                        'match_results_id' => $matchResult->id,
                        'quarter_number' => $quarterNumber, // Tambahkan quarter_number
                        'per' => $per,
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
            }

            // Update next round matches
            $this->updateNextRoundMatches($schedule, $winningTeamId);

            DB::commit();

            return redirect()->route('dashboard.jadwal', $id_tournament)
                ->with('success', 'Hasil pertandingan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan hasil pertandingan: ' . $e->getMessage()); // Tambahkan logging
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function edit($id_tournament, $id_schedule)
    {
        $tournament = Tournament::findOrFail($id_tournament);
        $schedule = Schedule::findOrFail($id_schedule);

        $matchResult = $schedule->matchResult;

        if (!$matchResult) {
            return redirect()->back()->with('error', 'Hasil pertandingan tidak ditemukan.');
        }

        $team1 = $schedule->team1;
        $team2 = $schedule->team2;

        $players1 = Player::where('teams_id', $team1->id)->get();
        $players2 = Player::where('teams_id', $team2->id)->get();

        // Ambil data quarter_results yang sudah ada
        $quarterResults = QuarterResult::where('match_results_id', $matchResult->id)
            ->orderBy('quarter_number')
            ->get()
            ->keyBy('quarter_number'); // Key by quarter_number untuk memudahkan akses di view

        // Ambil data player_stats yang sudah ada
        // Kita akan group berdasarkan player_id, lalu di dalamnya group berdasarkan quarter_number
        $playerStats = PlayerStat::where('match_results_id', $matchResult->id)
            ->get()
            ->groupBy('players_id')
            ->map(function ($playerStatCollection) {
                return $playerStatCollection->keyBy('quarter_number');
            });


        return view('match_results.edit', compact('tournament', 'schedule', 'team1', 'team2', 'players1', 'players2', 'matchResult', 'playerStats', 'quarterResults'));
    }
    public function update(Request $request, $id_tournament, $id_schedule)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'quarter_scores.*.team1_score' => 'required|integer|min:0',
                'quarter_scores.*.team2_score' => 'required|integer|min:0',
                'player_stats.*.*.point' => 'nullable|integer|min:0',
                'player_stats.*.*.fgm' => 'nullable|integer|min:0',
                'player_stats.*.*.fga' => 'nullable|integer|min:0',
                'player_stats.*.*.fta' => 'nullable|integer|min:0',
                'player_stats.*.*.ftm' => 'nullable|integer|min:0',
                'player_stats.*.*.orb' => 'nullable|integer|min:0',
                'player_stats.*.*.drb' => 'nullable|integer|min:0',
                'player_stats.*.*.stl' => 'nullable|integer|min:0',
                'player_stats.*.*.ast' => 'nullable|integer|min:0',
                'player_stats.*.*.blk' => 'nullable|integer|min:0',
                'player_stats.*.*.pf' => 'nullable|integer|min:0',
                'player_stats.*.*.to' => 'nullable|integer|min:0',
            ]);

            $schedule = Schedule::findOrFail($id_schedule);
            $matchResult = $schedule->matchResult;

            if (!$matchResult) {
                return redirect()->back()->with('error', 'Hasil pertandingan tidak ditemukan.');
            }

            $previousWinner = $matchResult->winning_team_id;
            $previousLoser = $matchResult->losing_team_id;

            // Hitung skor total baru dari semua kuarter
            $newTotalTeam1Score = 0;
            $newTotalTeam2Score = 0;
            foreach ($request->quarter_scores as $quarterData) {
                $newTotalTeam1Score += $quarterData['team1_score'];
                $newTotalTeam2Score += $quarterData['team2_score'];
            }

            $newWinner = $newTotalTeam1Score > $newTotalTeam2Score
                ? $schedule->team1_id
                : $schedule->team2_id;

            $newLoser = $newTotalTeam1Score < $newTotalTeam2Score
                ? $schedule->team1_id
                : $schedule->team2_id;

            // Perbarui MatchResult utama
            $matchResult->update([
                'team1_score' => $newTotalTeam1Score,
                'team2_score' => $newTotalTeam2Score,
                'winning_team_id' => $newWinner,
                'losing_team_id' => $newLoser,
            ]);

            // Perbarui Quarter Results (gunakan updateOrCreate)
            foreach ($request->quarter_scores as $quarterNumber => $quarterData) {
                QuarterResult::updateOrCreate(
                    ['match_results_id' => $matchResult->id, 'quarter_number' => $quarterNumber],
                    [
                        'team1_score' => $quarterData['team1_score'],
                        'team2_score' => $quarterData['team2_score'],
                    ]
                );
            }

            // Hapus quarter_results yang mungkin tidak ada lagi (jika jumlah kuarter berkurang)
            $existingQuarterNumbers = array_keys($request->quarter_scores);
            QuarterResult::where('match_results_id', $matchResult->id)
                ->whereNotIn('quarter_number', $existingQuarterNumbers)
                ->delete();

            // Perbarui statistik tim jika pemenang berubah
            if ($previousWinner != $newWinner || $previousLoser != $newLoser) {
                // Kurangi statistik tim sebelumnya
                DB::table('team_stats')
                    ->where('teams_id', $previousWinner)
                    ->where('tournaments_id', $id_tournament)
                    ->update([
                        'wins' => DB::raw('GREATEST(wins - 1, 0)')
                    ]);

                DB::table('team_stats')
                    ->where('teams_id', $previousLoser)
                    ->where('tournaments_id', $id_tournament)
                    ->update([
                        'losses' => DB::raw('GREATEST(losses - 1, 0)')
                    ]);

                // Tambah statistik tim baru
                DB::table('team_stats')
                    ->updateOrInsert(
                        ['teams_id' => $newWinner, 'tournaments_id' => $id_tournament],
                        ['wins' => DB::raw('COALESCE(wins, 0) + 1'), 'losses' => DB::raw('COALESCE(losses, 0)')]
                    );

                DB::table('team_stats')
                    ->updateOrInsert(
                        ['teams_id' => $newLoser, 'tournaments_id' => $id_tournament],
                        ['losses' => DB::raw('COALESCE(losses, 0) + 1'), 'wins' => DB::raw('COALESCE(wins, 0)')]
                    );
            }


            // Perbarui statistik pemain per kuarter
            foreach ($request->player_stats as $playerId => $quarters) {
                foreach ($quarters as $quarterNumber => $stats) {
                    $per = $this->calculatePER($stats);

                    PlayerStat::updateOrCreate(
                        ['players_id' => $playerId, 'match_results_id' => $matchResult->id, 'quarter_number' => $quarterNumber],
                        [
                            'per' => $per,
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
            }

            // Hapus player_stats yang tidak ada di request lagi (jika ada kuarter atau pemain yang dihapus)
            $requestedPlayerQuarterStats = [];
            foreach ($request->player_stats as $playerId => $quarters) {
                foreach ($quarters as $quarterNumber => $stats) {
                    $requestedPlayerQuarterStats[] = ['players_id' => $playerId, 'quarter_number' => $quarterNumber];
                }
            }

            $playerStatsToDelete = PlayerStat::where('match_results_id', $matchResult->id)->get();
            foreach ($playerStatsToDelete as $ps) {
                $found = false;
                foreach ($requestedPlayerQuarterStats as $rpqs) {
                    if ($ps->players_id == $rpqs['players_id'] && $ps->quarter_number == $rpqs['quarter_number']) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $ps->delete();
                }
            }


            // Update next round matches if winner changed
            if ($previousWinner != $newWinner) {
                $this->updateNextRoundMatches($schedule, $newWinner, $previousWinner);
            }

            DB::commit();

            return redirect()->route('dashboard.jadwal', $id_tournament)
                ->with('success', 'Hasil pertandingan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui hasil pertandingan: ' . $e->getMessage()); // Tambahkan logging
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    // Fungsi updateNextRoundMatches dan calculatePER tetap sama
    protected function updateNextRoundMatches(Schedule $currentMatch, $newWinnerId, $oldWinnerId = null)
    {
        $nextRound = $currentMatch->round + 1;
        $tournamentId = $currentMatch->tournaments_id;

        $nextRoundMatches = Schedule::where('tournaments_id', $tournamentId)
            ->where('round', $nextRound)
            ->get();

        foreach ($nextRoundMatches as $nextMatch) {
            $updateData = [];

            if ($this->isSourceMatch($currentMatch, $nextMatch, 'team1')) {
                if ($oldWinnerId && $nextMatch->team1_id == $oldWinnerId) {
                    $updateData['team1_id'] = $newWinnerId;
                } elseif ($nextMatch->team1_id === null) {
                    $updateData['team1_id'] = $newWinnerId;
                }
            }

            if ($this->isSourceMatch($currentMatch, $nextMatch, 'team2')) {
                if ($oldWinnerId && $nextMatch->team2_id == $oldWinnerId) {
                    $updateData['team2_id'] = $newWinnerId;
                } elseif ($nextMatch->team2_id === null) {
                    $updateData['team2_id'] = $newWinnerId;
                }
            }

            if (!empty($updateData)) {
                $nextMatch->update($updateData);
            }
        }
    }

    protected function isSourceMatch(Schedule $currentMatch, Schedule $nextMatch, $teamPosition)
    {
        $currentMatchOrder = Schedule::where('tournaments_id', $currentMatch->tournaments_id)
            ->where('round', $currentMatch->round)
            ->where('id', '<=', $currentMatch->id)
            ->count();

        $nextMatchOrder = Schedule::where('tournaments_id', $nextMatch->tournaments_id)
            ->where('round', $nextMatch->round)
            ->where('id', '<=', $nextMatch->id)
            ->count();

        if ($teamPosition == 'team1') {
            return (2 * $nextMatchOrder - 1) == $currentMatchOrder;
        } else {
            return (2 * $nextMatchOrder) == $currentMatchOrder;
        }
    }

    private function calculatePER($stats)
    {
        return (
            ($stats['point'] ?? 0) +
            (($stats['fgm'] ?? 0) * 0.4) +
            (($stats['fga'] ?? 0) * -0.7) +
            ((($stats['fta'] ?? 0) - ($stats['ftm'] ?? 0)) * -0.4) +
            (($stats['orb'] ?? 0) * 0.7) +
            (($stats['drb'] ?? 0) * 0.3) +
            ($stats['stl'] ?? 0) +
            (($stats['ast'] ?? 0) * 0.7) +
            (($stats['blk'] ?? 0) * 0.7) +
            (($stats['pf'] ?? 0) * -0.4) -
            ($stats['to'] ?? 0)
        );
    }
}
