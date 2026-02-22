<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TournamentController extends Controller
{
    public function index(Request $request)
    {

        $now = Carbon::now();

        Tournament::whereDate('start_date', '<=', $now->toDateString())
            ->whereDate('end_date', '>=', $now->toDateString())
            ->where('status', '!=', 'ongoing')
            ->update(['status' => 'ongoing']);

        Tournament::whereDate('end_date', '=', $now->copy()->subDay()->toDateString())
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed']);

        $query = Tournament::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $tournaments = $query
            ->orderByRaw("CASE 
                WHEN status = 'upcoming' THEN 1 
                WHEN status = 'scheduled' THEN 2 
                WHEN status = 'ongoing' THEN 3 
                WHEN status = 'completed' THEN 4 
                ELSE 5 END")
            ->get();

        return view('welcome', compact('tournaments'));
    }

    public function allTournaments(Request $request)
    {
        $now = Carbon::now();

        Tournament::whereDate('start_date', '<=', $now->toDateString())
            ->whereDate('end_date', '>=', $now->toDateString())
            ->where('status', '!=', 'ongoing')
            ->update(['status' => 'ongoing']);

        Tournament::whereDate('end_date', '=', $now->copy()->subDay()->toDateString())
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed']);

        $query = Tournament::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $tournaments = $query
            ->orderByRaw("CASE 
                WHEN status = 'ongoing' THEN 1 
                WHEN status = 'upcoming' THEN 2 
                WHEN status = 'scheduled' THEN 3 
                WHEN status = 'completed' THEN 4 
                ELSE 5 END")
            ->paginate(12);

        return view('general.tournaments', compact('tournaments'));
    }



    public function myTournaments()
    {
        $user = Auth::user();

        if ($user->role == 1) {

            $tournaments = Tournament::where('users_id', $user->id)->get();
        } elseif ($user->role == 2) {
            $tournaments = Tournament::whereHas('teams', function ($query) use ($user) {
                $query->where('teams.id', $user->team_id);
            })->get();
        } else {
            $tournaments = collect();
        }

        return view('tournaments.tourney_saya', compact('tournaments', 'user'));
    }



    public function detail($id)
    {
        $tournament = Tournament::findOrFail($id);

        // --- Overview Stats ---
        $teams = $tournament->teams()->with('players')->get();
        $totalTeams = $teams->count();
        $totalPlayers = $teams->sum(fn($t) => $t->players->count());

        // Schedules for this tournament
        $schedules = \App\Models\Schedule::where('tournaments_id', $id)
            ->with(['team1', 'team2', 'matchResult'])
            ->orderBy('date', 'asc')
            ->get();

        $totalMatches = $schedules->count();
        $matchesCompleted = $schedules->filter(fn($s) => $s->status === 'completed')->count();
        $matchesRemaining = $totalMatches - $matchesCompleted;

        // --- Next Match Logic ---
        $allMatchesCompleted = ($totalMatches > 0 && $matchesRemaining === 0);
        $nextMatch = null;
        if (!$allMatchesCompleted && $totalMatches > 0) {
            // Find first schedule that hasn't ended, ordered by date
            $nextMatch = $schedules->firstWhere('status', '!=', 'end');
            // If all are not ended (none started), just show first match
            if (!$nextMatch) {
                $nextMatch = $schedules->first();
            }
        }

        // --- Top 3 Teams (by wins) ---
        $teamIds = $teams->pluck('id');
        $topTeams = \App\Models\MatchResult::whereIn('winning_team_id', $teamIds)
            ->whereHas('schedule', fn($q) => $q->where('tournaments_id', $id))
            ->select('winning_team_id', \DB::raw('COUNT(*) as wins'))
            ->groupBy('winning_team_id')
            ->orderByDesc('wins')
            ->take(3)
            ->get()
            ->map(function($item) {
                $team = \App\Models\Team::find($item->winning_team_id);
                return (object)[
                    'name' => $team->name ?? 'Unknown',
                    'logo' => $team->logo ?? null,
                    'wins' => $item->wins,
                ];
            });

        // --- Top 3 Players (by best average PER) ---
        $matchResultIds = $schedules->pluck('matchResult.id')->filter();
        $topPlayers = collect();
        if ($matchResultIds->isNotEmpty()) {
            $topPlayers = \App\Models\PlayerStat::whereIn('match_results_id', $matchResultIds)
                ->select('players_id', \DB::raw('ROUND(AVG(per), 1) as avg_per'))
                ->groupBy('players_id')
                ->orderByDesc('avg_per')
                ->take(3)
                ->get()
                ->map(function($item) {
                    $player = \App\Models\Player::with('team')->find($item->players_id);
                    return (object)[
                        'name' => $player->name ?? 'Unknown',
                        'team_name' => $player->team->name ?? '-',
                        'team_logo' => $player->team->logo ?? null,
                        'avg_per' => $item->avg_per,
                    ];
                });
        }

        return view('tournaments.detail', compact(
            'tournament', 'totalTeams', 'totalPlayers',
            'totalMatches', 'matchesCompleted', 'matchesRemaining',
            'allMatchesCompleted', 'nextMatch',
            'topTeams', 'topPlayers'
        ));
    }


    public function show($id)
    {
        $tournament = Tournament::findOrFail($id);
        return view('dashboard.bracket', compact('tournament'));
    }
    public function edit($id)
    {
        $tournament = Tournament::findOrFail($id);

        if (Auth::id() !== $tournament->users_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit turnamen ini.');
        }

        return view('tournaments.edit', compact('tournament'));
    }

    public function update(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);

        if (Auth::id() !== $tournament->users_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit turnamen ini.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:upcoming,scheduled,ongoing,completed',
        ]);

        $tournament->update($request->all());

        return redirect()->route('tournament.detail', $id)->with('success', 'Turnamen berhasil diperbarui.');
    }

    public function create()
    {
        return view('tournaments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $tournament = Tournament::create([
            'name' => $request->name,
            'organizer' => $request->organizer,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'Upcoming',
            'users_id' => Auth::id(),
        ]);

        return redirect()->route('tournament.mine')->with('success', 'Turnamen berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $tournament = Tournament::findOrFail($id);
        $tournament->delete();

        return redirect()->route('tournament.mine')->with('success', 'Turnamen berhasil dihapus.');
    }
}
