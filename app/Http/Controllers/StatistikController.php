<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\PlayerStat;
use Illuminate\Http\Request;

class StatistikController extends Controller
{
    public function show(Request $request, $tournamentId)
    {
        // Ambil data turnamen berdasarkan ID
        $tournament = Tournament::findOrFail($tournamentId);

        // Ambil semua tim yang berpartisipasi dalam turnamen
        $teams = $tournament->teams;

        // Ambil parameter filter dari request
        $teamId = $request->input('team_id');

        // Ambil pemain berdasarkan tournament_id dan filter tim jika ada
        $playersQuery = Player::whereHas('team', function ($query) use ($tournamentId) {
            $query->whereHas('tournaments', function ($subQuery) use ($tournamentId) {
                $subQuery->where('tournaments.id', $tournamentId);
            });
        })->with('playerStats');

        if ($teamId) {
            $playersQuery->whereHas('team', function ($query) use ($teamId) {
                $query->where('teams.id', $teamId);
            });
        }

        $players = $playersQuery->get();

        // Hitung statistik total atau rata-rata setiap player
        foreach ($players as $player) {
            $player->total_stats = $player->playerStats->reduce(function ($carry, $stat, $index) use ($player) {
                $carry['point'] += $stat->point;
                $carry['fgm'] += $stat->fgm;
                $carry['fga'] += $stat->fga;
                $carry['fta'] += $stat->fta;
                $carry['ftm'] += $stat->ftm;
                $carry['orb'] += $stat->orb;
                $carry['drb'] += $stat->drb;
                $carry['stl'] += $stat->stl;
                $carry['ast'] += $stat->ast;
                $carry['blk'] += $stat->blk;
                $carry['pf'] += $stat->pf;
                $carry['to'] += $stat->to;

                // Hitung PER berdasarkan statistik
                $carry['per'] += $stat->point +
                    ($stat->fgm * 0.4) +
                    ($stat->fga * -0.7) +
                    (($stat->fta - $stat->ftm) * -0.4) +
                    ($stat->orb * 0.7) +
                    ($stat->drb * 0.3) +
                    $stat->stl +
                    ($stat->ast * 0.7) +
                    ($stat->blk * 0.7) +
                    ($stat->pf * -0.4) -
                    $stat->to;

                if ($index === count($player->playerStats) - 1) {
                    $carry['point'] /= count($player->playerStats);
                    $carry['fgm'] /= count($player->playerStats);
                    $carry['fga'] /= count($player->playerStats);
                    $carry['fta'] /= count($player->playerStats);
                    $carry['ftm'] /= count($player->playerStats);
                    $carry['orb'] /= count($player->playerStats);
                    $carry['drb'] /= count($player->playerStats);
                    $carry['stl'] /= count($player->playerStats);
                    $carry['ast'] /= count($player->playerStats);
                    $carry['blk'] /= count($player->playerStats);
                    $carry['pf'] /= count($player->playerStats);
                    $carry['to'] /= count($player->playerStats);

                    // Menghitung ulang PER menggunakan rata-rata
                    $carry['per'] = (
                        $carry['point'] +
                        ($carry['fgm'] * 0.4) +
                        ($carry['fga'] * -0.7) +
                        (($carry['fta'] - $carry['ftm']) * -0.4) +
                        ($carry['orb'] * 0.7) +
                        ($carry['drb'] * 0.3) +
                        $carry['stl'] +
                        ($carry['ast'] * 0.7) +
                        ($carry['blk'] * 0.7) +
                        ($carry['pf'] * -0.4) -
                        $carry['to']
                    );
                }

                // **UPDATE ke database**
                PlayerStat::where('players_id', $player->id)->update(['per' => $carry['per']]);

                return $carry;
            }, [
                'point' => 0,
                'fgm' => 0,
                'fga' => 0,
                'fta' => 0,
                'ftm' => 0,
                'orb' => 0,
                'drb' => 0,
                'stl' => 0,
                'ast' => 0,
                'blk' => 0,
                'pf' => 0,
                'to' => 0,
                'per' => 0,
            ]);
        }

        return view('dashboard.statistik', compact('tournament', 'teams', 'players', 'teamId'));
    }
}
