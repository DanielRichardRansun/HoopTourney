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

        // Update status ke 'ongoing'
        Tournament::whereDate('start_date', '<=', $now->toDateString())
            ->whereDate('end_date', '>=', $now->toDateString())
            ->where('status', '!=', 'ongoing')
            ->update(['status' => 'ongoing']);

        // Update status ke 'completed' jika hari ini adalah end_date + 1
        Tournament::whereDate('end_date', '=', $now->copy()->subDay()->toDateString())
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed']);

        // Query dan FILTER
        $query = Tournament::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $tournaments = $query
            ->orderByRaw("FIELD(status, 'upcoming','scheduled', 'ongoing', 'completed')")
            ->get();

        return view('welcome', compact('tournaments'));
    }



    public function myTournaments()
    {
        $user = Auth::user();

        if ($user->role == 1) {
            // Jika admin, tampilkan turnamen yang mereka buat
            $tournaments = Tournament::where('users_id', $user->id)->get();
        } elseif ($user->role == 2) {
            // Jika team, tampilkan turnamen yang diikuti oleh tim mereka
            $tournaments = Tournament::whereHas('teams', function ($query) use ($user) {
                $query->where('teams.id', $user->team_id);
            })->get();
        } else {
            // Jika bukan admin atau team, kosongkan list turnamen
            $tournaments = collect();
        }

        return view('tournaments.tourney_saya', compact('tournaments', 'user'));
    }



    public function detail($id)
    {
        // Mengambil data turnamen berdasarkan ID
        $tournament = Tournament::findOrFail($id);

        // Mengirim data turnamen ke view detail_lomba.blade.php
        return view('tournaments.detail', compact('tournament'));
    }


    public function show($id)
    {
        // Mencari turnamen berdasarkan ID
        $tournament = Tournament::findOrFail($id);
        // Mengirim data turnamen ke view dashboard atau detail turnamen
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

        // Simpan Tournament
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
