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
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class MatchResultController extends Controller
{
    public function show($id_tournament, $id_schedule)
    {
        $tournament = Tournament::findOrFail($id_tournament);

        $schedule = Schedule::with(['team1.players', 'team2.players', 'matchResult'])->findOrFail($id_schedule);
        $matchResult = $schedule->matchResult;

        $playerStats = PlayerStat::where('match_results_id', $matchResult?->id)
            ->with('player.team')
            ->get();

        return view('match_results.show', compact('schedule', 'matchResult', 'playerStats', 'tournament'));
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
                'team1_score' => 'required|integer|min:0',
                'team2_score' => 'required|integer|min:0',
            ]);

            $schedule = Schedule::findOrFail($id_schedule);

            $winningTeamId = $request->team1_score > $request->team2_score
                ? $schedule->team1_id
                : $schedule->team2_id;

            $losingTeamId = $request->team1_score < $request->team2_score
                ? $schedule->team1_id
                : $schedule->team2_id;

            $matchResult = MatchResult::create([
                'team1_score' => $request->team1_score,
                'team2_score' => $request->team2_score,
                'winning_team_id' => $winningTeamId,
                'losing_team_id' => $losingTeamId,
                'schedules_id' => $schedule->id,
            ]);

            $schedule->update(['status' => 'Completed']);

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

            foreach ($request->player_stats as $playerId => $stats) {
                $per = $this->calculatePER($stats);

                PlayerStat::create([
                    'players_id' => $playerId,
                    'match_results_id' => $matchResult->id,
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

            // Update next round matches
            $this->updateNextRoundMatches($schedule, $winningTeamId);

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

        $playerStats = PlayerStat::where('match_results_id', $matchResult->id)->get()->keyBy('players_id');

        return view('match_results.edit', compact('tournament', 'schedule', 'team1', 'team2', 'players1', 'players2', 'matchResult', 'playerStats'));
    }

    public function update(Request $request, $id_tournament, $id_schedule)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'team1_score' => 'required|integer|min:0',
                'team2_score' => 'required|integer|min:0',
            ]);

            $schedule = Schedule::findOrFail($id_schedule);
            $matchResult = $schedule->matchResult;

            if (!$matchResult) {
                return redirect()->back()->with('error', 'Hasil pertandingan tidak ditemukan.');
            }

            $previousWinner = $matchResult->winning_team_id;
            $previousLoser = $matchResult->losing_team_id;

            $newWinner = $request->team1_score > $request->team2_score
                ? $schedule->team1_id
                : $schedule->team2_id;

            $newLoser = $request->team1_score < $request->team2_score
                ? $schedule->team1_id
                : $schedule->team2_id;

            $matchResult->update([
                'team1_score' => $request->team1_score,
                'team2_score' => $request->team2_score,
                'winning_team_id' => $newWinner,
                'losing_team_id' => $newLoser,
            ]);

            DB::beginTransaction();

            try {
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

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            foreach ($request->player_stats as $playerId => $stats) {
                $per = $this->calculatePER($stats);

                PlayerStat::updateOrCreate(
                    ['players_id' => $playerId, 'match_results_id' => $matchResult->id],
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

            // Update next round matches if winner changed
            if ($previousWinner != $newWinner) {
                $this->updateNextRoundMatches($schedule, $newWinner, $previousWinner);
            }

            DB::commit();

            return redirect()->route('dashboard.jadwal', $id_tournament)
                ->with('success', 'Hasil pertandingan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    protected function updateNextRoundMatches(Schedule $currentMatch, $newWinnerId, $oldWinnerId = null)
    {
        $nextRound = $currentMatch->round + 1;
        $tournamentId = $currentMatch->tournaments_id;

        // Cari semua pertandingan di round berikutnya
        $nextRoundMatches = Schedule::where('tournaments_id', $tournamentId)
            ->where('round', $nextRound)
            ->get();

        foreach ($nextRoundMatches as $nextMatch) {
            $updateData = [];

            // Cek apakah current match adalah sumber untuk team1 di next match
            if ($this->isSourceMatch($currentMatch, $nextMatch, 'team1')) {
                if ($oldWinnerId && $nextMatch->team1_id == $oldWinnerId) {
                    $updateData['team1_id'] = $newWinnerId;
                } elseif ($nextMatch->team1_id === null) {
                    $updateData['team1_id'] = $newWinnerId;
                }
            }

            // Cek apakah current match adalah sumber untuk team2 di next match
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
        // Hitung posisi current match dalam round-nya
        $currentMatchOrder = Schedule::where('tournaments_id', $currentMatch->tournaments_id)
            ->where('round', $currentMatch->round)
            ->where('id', '<=', $currentMatch->id)
            ->count();

        // Hitung posisi next match dalam round-nya
        $nextMatchOrder = Schedule::where('tournaments_id', $nextMatch->tournaments_id)
            ->where('round', $nextMatch->round)
            ->where('id', '<=', $nextMatch->id)
            ->count();

        // Untuk single elimination, setiap match di round n akan menjadi sumber untuk 1 match di round n+1
        // Team1 di next match berasal dari match (2*nextMatchOrder - 1) di round sebelumnya
        // Team2 di next match berasal dari match (2*nextMatchOrder) di round sebelumnya
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

    // private function getGoogleClient()
    // {
    //     $client = new Client();
    //     $client->setApplicationName('Your App Name');
    //     $client->setScopes([Sheets::SPREADSHEETS]);
    //     $client->setAuthConfig(storage_path('app/credentials.json')); // Your Google API credentials
    //     $client->setAccessType('offline');

    //     return $client;
    // }

    // public function downloadTemplate($id_tournament, $id_schedule)
    // {
    //     $schedule = Schedule::with(['team1.players', 'team2.players'])->findOrFail($id_schedule);

    //     $client = $this->getGoogleClient();
    //     $service = new Sheets($client);

    //     try {
    //         // Create new spreadsheet
    //         $spreadsheet = new \Google_Service_Sheets_Spreadsheet([
    //             'properties' => [
    //                 'title' => "Match Result Template - {$schedule->team1->name} vs {$schedule->team2->name}"
    //             ]
    //         ]);

    //         $spreadsheet = $service->spreadsheets->create($spreadsheet);
    //         $spreadsheetId = $spreadsheet->spreadsheetId;

    //         // Prepare data
    //         $values = [
    //             ['Match Result Template'],
    //             ['Team 1', $schedule->team1->name],
    //             ['Team 2', $schedule->team2->name],
    //             [''],
    //             ['Player Stats'],
    //             ['Team', 'Player ID', 'Player Name', 'Points', 'FGM', 'FGA', 'FTA', 'FTM', 'ORB', 'DRB', 'STL', 'AST', 'BLK', 'PF', 'TO']
    //         ];

    //         // Add team1 players
    //         foreach ($schedule->team1->players as $player) {
    //             $values[] = [
    //                 $schedule->team1->name,
    //                 $player->id,
    //                 $player->name,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0
    //             ];
    //         }

    //         // Add team2 players
    //         foreach ($schedule->team2->players as $player) {
    //             $values[] = [
    //                 $schedule->team2->name,
    //                 $player->id,
    //                 $player->name,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0,
    //                 0
    //             ];
    //         }

    //         // Write data to sheet
    //         $body = new ValueRange(['values' => $values]);
    //         $params = [
    //             'valueInputOption' => 'RAW'
    //         ];

    //         $service->spreadsheets_values->update(
    //             $spreadsheetId,
    //             'A1',
    //             $body,
    //             $params
    //         );

    //         // Formatting
    //         $requests = [
    //             new \Google_Service_Sheets_Request([
    //                 'repeatCell' => [
    //                     'range' => [
    //                         'sheetId' => 0,
    //                         'startRowIndex' => 0,
    //                         'endRowIndex' => 1
    //                     ],
    //                     'cell' => [
    //                         'userEnteredFormat' => [
    //                             'textFormat' => [
    //                                 'bold' => true,
    //                                 'fontSize' => 14
    //                             ]
    //                         ]
    //                     ],
    //                     'fields' => 'userEnteredFormat.textFormat'
    //                 ]
    //             ])
    //         ];

    //         $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
    //             'requests' => $requests
    //         ]);

    //         $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);

    //         // Get shareable link
    //         $drive = new \Google_Service_Drive($client);
    //         $permission = new \Google_Service_Drive_Permission([
    //             'type' => 'anyone',
    //             'role' => 'writer'
    //         ]);
    //         $drive->permissions->create($spreadsheetId, $permission);

    //         return redirect($spreadsheet->spreadsheetUrl);
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Failed to create template: ' . $e->getMessage());
    //     }
    // }

    // public function importFromSheet(Request $request, $id_tournament, $id_schedule)
    // {
    //     $request->validate([
    //         'sheet_url' => 'required|url'
    //     ]);

    //     $schedule = Schedule::findOrFail($id_schedule);

    //     try {
    //         $client = $this->getGoogleClient();
    //         $service = new Sheets($client);

    //         // Extract spreadsheet ID from URL
    //         $urlParts = parse_url($request->sheet_url);
    //         parse_str($urlParts['query'], $queryParams);
    //         $spreadsheetId = $queryParams['spreadsheetId'] ?? null;

    //         if (!$spreadsheetId) {
    //             throw new \Exception('Invalid Google Sheets URL');
    //         }

    //         // Read data
    //         $range = 'A1:O100'; // Adjust as needed
    //         $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    //         $values = $response->getValues();

    //         if (empty($values)) {
    //             throw new \Exception('No data found in sheet');
    //         }

    //         // Process data
    //         $team1Score = null;
    //         $team2Score = null;
    //         $playerStats = [];

    //         foreach ($values as $row) {
    //             if (count($row) < 2) continue;

    //             // Check for scores
    //             if ($row[0] === 'Team 1' && is_numeric($row[1])) {
    //                 $team1Score = (int)$row[1];
    //             } elseif ($row[0] === 'Team 2' && is_numeric($row[1])) {
    //                 $team2Score = (int)$row[1];
    //             }

    //             // Check for player stats
    //             if (count($row) >= 15 && is_numeric($row[1])) {
    //                 $playerStats[$row[1]] = [
    //                     'point' => (int)$row[3],
    //                     'fgm' => (int)$row[4],
    //                     'fga' => (int)$row[5],
    //                     'fta' => (int)$row[6],
    //                     'ftm' => (int)$row[7],
    //                     'orb' => (int)$row[8],
    //                     'drb' => (int)$row[9],
    //                     'stl' => (int)$row[10],
    //                     'ast' => (int)$row[11],
    //                     'blk' => (int)$row[12],
    //                     'pf' => (int)$row[13],
    //                     'to' => (int)$row[14],
    //                 ];
    //             }
    //         }

    //         if (!$team1Score || !$team2Score) {
    //             throw new \Exception('Team scores not found in sheet');
    //         }

    //         // Prepare request data
    //         $requestData = new Request([
    //             'team1_score' => $team1Score,
    //             'team2_score' => $team2Score,
    //             'player_stats' => $playerStats
    //         ]);

    //         // Determine if we're creating or updating
    //         if ($schedule->matchResult) {
    //             return $this->update($requestData, $id_tournament, $id_schedule);
    //         } else {
    //             return $this->store($requestData, $id_tournament, $id_schedule);
    //         }
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Failed to import from sheet: ' . $e->getMessage());
    //     }
    // }
}
