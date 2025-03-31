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
            ->with('matchResult') // Eager load matchResult untuk efisiensi query
            ->orderBy('round')
            ->get();

        if ($schedules->isEmpty()) {
            return view('dashboard.bracket', compact('tournament'))->with('message', 'Belum ada jadwal pertandingan.');
        }

        $bracket = [];
        $champion = 'TBD'; // Default value jika belum ada pemenang

        foreach ($schedules as $schedule) {
            $round = $schedule->round;
            $team1 = Team::find($schedule->team1_id)->name ?? 'TBD';
            $team2 = Team::find($schedule->team2_id)->name ?? 'TBD';

            // Cek apakah ini round terakhir dan ada hasil pertandingan
            if ($schedule->round == $schedules->max('round') && $schedule->matchResult) {
                $champion = Team::find($schedule->matchResult->winning_team_id)->name ?? 'TBD';
            }

            $bracket[$round][] = [$team1, $team2];
        }

        return view('dashboard.bracket', compact('tournament', 'bracket', 'champion'));
    }
}
