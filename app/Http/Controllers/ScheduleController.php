<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Schedule;
use App\Models\Team;
use App\Models\TeamTournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class ScheduleController extends Controller
{
    private function checkTournamentAdminAccess($tournamentId)
    {
        $tournament = Tournament::find($tournamentId);
        $user = Auth::user();

        // Debugging: Cek apakah user berhasil didapatkan
        if (!$user) {
            abort(403, 'User not authenticated.');
        }

        // Debugging: Cek apakah tournament ditemukan
        if (!$tournament) {
            abort(403, 'Tournament not found.');
        }

        // Sesuaikan dengan nilai integer di database (1 = admin)
        if ($user->role !== 1) {
            abort(403, 'User is not an admin.');
        }

        // Cek apakah user memiliki tournament tersebut
        if ($tournament->users_id !== $user->id) {
            abort(403, 'User does not own this tournament.');
        }
    }

    public function show($id)
    {
        $tournament = Tournament::findOrFail($id);
        $user = Auth::user();

        $isAdmin = $user->role == 1 && $tournament->users_id == $user->id;

        $tournamentTeams = Team::whereHas('teamTournament', function ($query) use ($id) {
            $query->where('tournament_id', $id);
        })->get();

        $schedules = Schedule::where('tournaments_id', $tournament->id)
            ->with(['team1', 'team2'])
            ->get();

        return view('dashboard.jadwal', [
            'tournament' => $tournament,
            'schedules' => $schedules,
            'tournamentTeams' => $tournamentTeams,
            'isAdmin' => $isAdmin
        ]);
    }


    public function update(Request $request, $id)
    {

        $schedule = Schedule::findOrFail($id);
        $schedule->update([
            'team1_id' => $request->team1_id,
            'team2_id' => $request->team2_id,
            'date' => $request->date,
            'location' => $request->location,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui!');
    }


    public function generateSchedule($id)
    {
        $this->checkTournamentAdminAccess($id);

        $tournament = Tournament::findOrFail($id);

        // Hapus jadwal lama jika ada
        Schedule::where('tournaments_id', $id)->delete();

        // Ambil bracket yang telah dihasilkan
        $bracket = $this->generateBracket($tournament);

        $matchIndex = 1;
        $round = 1;
        // $date = Carbon::parse($tournament->start_date);

        foreach ($bracket as $roundMatches) {
            foreach ($roundMatches as $match) {
                // Skip pertandingan di babak pertama jika ada tim yang bertemu dengan "Bye"
                if ($round === 1 && ($match[0] === '-' || $match[1] === '-')) {
                    continue;
                }

                // Buat jadwal pertandingan baru
                Schedule::create([
                    'team1_id' => $match[0] !== '-' ? $this->getTeamIdByName($match[0]) : null,
                    'team2_id' => $match[1] !== '-' ? $this->getTeamIdByName($match[1]) : null,
                    'date' => null,
                    'location' => null,
                    'status' => 'Scheduled',
                    'round' => $round,
                    'tournaments_id' => $tournament->id,
                ]);

                $matchIndex++;
            }
            $round++;
        }

        return redirect()->route('tournament.detail', $id)->with('success', 'Jadwal berhasil dibuat!');
    }

    private function generateBracket($tournament)
    {
        $teams = $tournament->teams()->pluck('name')->toArray();

        if (count($teams) === 0) {
            return [];
        }

        $totalTeams = count($teams);
        $powerOfTwo = 1;

        while ($powerOfTwo < $totalTeams) {
            $powerOfTwo *= 2;
        }

        $byesNeeded = $powerOfTwo - $totalTeams;
        $bracket = [];
        $matchupIndex = 0;
        $usedTeams = [];
        $nextRoundTeams = [];

        for ($i = 0; $i < $totalTeams; $i++) {
            if (count($usedTeams) < $totalTeams - $byesNeeded && !in_array($teams[$i], $usedTeams)) {
                if ($i + 1 < $totalTeams && !in_array($teams[$i + 1], $usedTeams)) {
                    $bracket[0][$matchupIndex++] = [$teams[$i], $teams[$i + 1]];
                    array_push($usedTeams, $teams[$i], $teams[$i + 1]);
                    $nextRoundTeams[] = 'Winner ' . ($matchupIndex);
                } else {
                    $bracket[0][$matchupIndex++] = [$teams[$i], '-'];
                    array_push($usedTeams, $teams[$i]);
                    $nextRoundTeams[] = $teams[$i];
                }
            }
        }

        foreach ($teams as $team) {
            if (!in_array($team, $usedTeams) && $byesNeeded > 0) {
                $bracket[0][$matchupIndex++] = [$team, '-'];
                $nextRoundTeams[] = $team;
                $byesNeeded--;
            }
        }

        $rounds = log($powerOfTwo, 2);
        for ($round = 1; $round < $rounds; $round++) {
            $matchupCount = count($nextRoundTeams) / 2;
            for ($matchup = 0; $matchup < $matchupCount; $matchup++) {
                $team1 = $nextRoundTeams[$matchup * 2] ?? 'Winner ' . (($matchup * 2) + 1);
                $team2 = $nextRoundTeams[($matchup * 2) + 1] ?? 'Winner ' . (($matchup * 2) + 2);
                $bracket[$round][$matchup] = [$team1, $team2];
            }
            $nextRoundTeams = array_map(function ($m) {
                return 'Winner ' . $m;
            }, range(1, count($bracket[$round])));
        }

        return $bracket;
    }

    private function getTeamIdByName($teamName)
    {
        return Team::where('name', $teamName)->value('id');
    }
}
