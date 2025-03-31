<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TeamStat;
use App\Models\MatchResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KlasemenController extends Controller
{
    public function show($tournamentId)
    {
        $tournament = Tournament::findOrFail($tournamentId);
        $teams = $tournament->teams;

        foreach ($teams as $team) {
            $teamStat = TeamStat::where('teams_id', $team->id)
                ->where('tournaments_id', $tournamentId)
                ->first();

            if ($teamStat) {
                $team->matches_played = $teamStat->wins + $teamStat->losses + ($teamStat->draws ?? 0);
                $team->wins = $teamStat->wins;
                $team->losses = $teamStat->losses;
            } else {
                $team->matches_played = 0;
                $team->wins = 0;
                $team->losses = 0;
            }

            // Hitung total poin yang diperoleh tim dari semua pertandingan
            $team->total_points = MatchResult::join('schedules', 'match_results.schedules_id', '=', 'schedules.id')
                ->where('schedules.tournaments_id', $tournamentId)
                ->where(function ($query) use ($team) {
                    $query->where('schedules.team1_id', $team->id)
                        ->orWhere('schedules.team2_id', $team->id);
                })
                ->sum(DB::raw("
                    CASE 
                        WHEN schedules.team1_id = {$team->id} THEN match_results.team1_score
                        WHEN schedules.team2_id = {$team->id} THEN match_results.team2_score
                        ELSE 0 
                    END
                "));

            // Hitung rata-rata poin per game
            $team->avg_points_per_game = $team->matches_played > 0
                ? round($team->total_points / $team->matches_played, 2)
                : 0;
        }

        return view('dashboard.klasemen', compact('tournament', 'teams'));
    }
}
