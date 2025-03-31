<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    // Menampilkan semua tim yang mengikuti turnamen tertentu
    public function index($id)
    {
        $tournament = Tournament::findOrFail($id);
        $teams = $tournament->teams;

        return view('dashboard.teams', compact('tournament', 'teams'));
    }

    // Menampilkan detail tim dalam turnamen tertentu
    public function show($tournament_id, $id)
    {
        $tournament = Tournament::findOrFail($tournament_id);
        $team = Team::whereHas('tournaments', function ($query) use ($tournament_id, $id) {
            $query->where('tournament_id', $tournament_id)->where('team_id', $id);
        })->firstOrFail();

        $players = Player::where('teams_id', $id)->get();

        return view('dashboard.team_detail', compact('team', 'players', 'tournament', 'tournament_id'));
    }

    // Menampilkan detail tim untuk halaman home
    public function showHome($id)
    {
        $team = Team::findOrFail($id);
        $players = Player::where('teams_id', $id)->get();

        return view('home_team_detail', compact('team', 'players'));
    }

    // Update informasi tim
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'coach' => 'required|string|max:255',
            'manager' => 'required|string|max:255',
        ]);

        $team = Team::findOrFail($id);
        $team->update($request->all());

        return redirect()->back()->with('success', 'Informasi tim berhasil diperbarui');
    }
}
