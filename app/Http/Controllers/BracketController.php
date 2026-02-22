<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Team;
use App\Models\Schedule;
use App\Models\MatchResult;
use Illuminate\Http\Request;

class BracketController extends Controller
{
    public function generateBracket($id)
    {
        $tournament = Tournament::findOrFail($id);
        $schedules = Schedule::where('tournaments_id', $id)
            ->with('matchResult')
            ->orderBy('id')
            ->get();

        if ($schedules->isEmpty()) {
            return view('dashboard.bracket', compact('tournament'))->with('message', 'Belum ada jadwal pertandingan.');
        }

        $bracket = [];
        $champion = 'TBD';

        foreach ($schedules as $schedule) {
            $round = $schedule->round;

            $team1Model = Team::find($schedule->team1_id);
            $team2Model = Team::find($schedule->team2_id);

            $team1Name = $team1Model->name ?? 'TBD';
            $team2Name = $team2Model->name ?? 'TBD';

            $winnerId = $schedule->matchResult->winning_team_id ?? null;

            $bracket[$round][] = [
                [
                    'name' => $team1Name,
                    'is_winner' => $schedule->matchResult?->winning_team_id == $schedule->team1_id,
                    'score' => $schedule->matchResult?->team1_score ?? null,
                    'logo' => $team1Model->logo ?? null
                ],
                [
                    'name' => $team2Name,
                    'is_winner' => $schedule->matchResult?->winning_team_id == $schedule->team2_id,
                    'score' => $schedule->matchResult?->team2_score ?? null,
                    'logo' => $team2Model->logo ?? null
                ],
                $schedule->id
            ];


            //camps
            if ($schedule->round == $schedules->max('round') && $winnerId) {
                $winTeam = Team::find($winnerId);
                $champion = (object)[
                    'name' => $winTeam->name ?? 'TBD',
                    'logo' => $winTeam->logo ?? null
                ];
            }
        }

        return view('dashboard.bracket', compact('tournament', 'bracket', 'champion'));
    }
}
