<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\PlayerStat;
use App\Models\MatchResult;
use Illuminate\Http\Request;

class StatistikController extends Controller
{
    public function show(Request $request, $tournamentId)
    {
        $tournament = Tournament::find($tournamentId);
        $teams = $tournament->teams;
        $teamId = $request->input('team_id');
        // Ambil semua match_result_id yang berkaitan dengan tournament ini
        $matchResultIds = MatchResult::whereHas('schedule', function ($query) use ($tournamentId) {
            $query->where('tournaments_id', $tournamentId);
        })->pluck('id');

        // Ambil semua team_id yang tergabung dalam turnamen
        $teamIds = $tournament->teams->pluck('id');

        // Ambil pemain dari tim-tim tersebut, dan ambil hanya player_stats yang match_result-nya termasuk dalam turnamen
        $playersQuery = Player::whereIn('teams_id', $teamIds)
            ->with(['playerStats' => function ($query) use ($matchResultIds) {
                $query->whereIn('match_results_id', $matchResultIds);
            }, 'team']);

        if ($teamId) {
            $playersQuery->where('teams_id', $teamId);
        }

        $players = $playersQuery->get();
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
                $carry['per'] += $stat->per;

                if ($index === count($player->playerStats) - 1) {
                    $carry['point'] = round($carry['point'] / count($player->playerStats), 2);
                    $carry['fgm'] = round($carry['fgm'] / count($player->playerStats), 2);
                    $carry['fga'] = round($carry['fga'] / count($player->playerStats), 2);
                    $carry['fta'] = round($carry['fta'] / count($player->playerStats), 2);
                    $carry['ftm'] = round($carry['ftm'] / count($player->playerStats), 2);
                    $carry['orb'] = round($carry['orb'] / count($player->playerStats), 2);
                    $carry['drb'] = round($carry['drb'] / count($player->playerStats), 2);
                    $carry['stl'] = round($carry['stl'] / count($player->playerStats), 2);
                    $carry['ast'] = round($carry['ast'] / count($player->playerStats), 2);
                    $carry['blk'] = round($carry['blk'] / count($player->playerStats), 2);
                    $carry['pf'] = round($carry['pf'] / count($player->playerStats), 2);
                    $carry['to'] = round($carry['to'] / count($player->playerStats), 2);
                    $carry['per'] = round($carry['per'] / count($player->playerStats), 2); // Rata-rata PER
                }

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
