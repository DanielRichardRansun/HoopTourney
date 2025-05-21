<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TournamentRequest;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\TeamStat;
use Illuminate\Support\Facades\Auth;
use App\Models\TeamTournament;
use Illuminate\Support\Facades\DB;

class TournamentRequestController extends Controller
{
    public function requestJoin($tournamentId)
    {
        $user = Auth::user();

        // Pastikan user memiliki team
        if (!$user->team_id) {
            return redirect()->back()->with('error', 'Anda belum tergabung dalam tim.');
        }

        // Cek apakah sudah mengajukan request sebelumnya
        $existingRequest = TournamentRequest::where('user_id', $user->id)
            ->where('team_id', $user->team_id)
            ->where('tournament_id', $tournamentId)
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan request.');
        }

        // Simpan request ke database
        TournamentRequest::create([
            'user_id' => $user->id,
            'team_id' => $user->team_id,
            'tournament_id' => $tournamentId,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Request berhasil dikirim!');
    }

    public function adminRequests(Request $request)
    {
        $user = Auth::user();

        if ($user->role != 1) {
            return redirect('/')->with('error', 'You dont have access to this page.');
        }

        // Start with tournaments owned by this admin
        $query = TournamentRequest::with(['user', 'team', 'tournament'])
            ->whereHas('tournament', function ($q) use ($user) {
                $q->where('users_id', $user->id);
            })
            ->latest();

        // Status filter
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Tournament filter (only shows tournaments owned by this admin)
        if ($request->has('tournament') && $request->tournament != 'all') {
            $query->where('tournament_id', $request->tournament);
        }

        $requests = $query->get();
        $tournaments = Tournament::where('users_id', $user->id)->get(); // Only get tournaments owned by this admin

        return view('admin.tournament_requests', compact('requests', 'tournaments'));
    }

    public function approveRequest($id)
    {
        $user = Auth::user();

        if ($user->role != 1) {
            return redirect('/')->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        DB::beginTransaction();
        try {
            $request = TournamentRequest::findOrFail($id);

            // Update status request
            $request->update(['status' => 'approved']);

            // Tambahkan team ke tournament
            TeamTournament::firstOrCreate([
                'team_id' => $request->team_id,
                'tournament_id' => $request->tournament_id
            ]);

            // Buat record team_stats untuk tim ini di tournament ini
            TeamStat::firstOrCreate([
                'teams_id' => $request->team_id,
                'tournaments_id' => $request->tournament_id
            ], [
                'wins' => 0,
                'losses' => 0
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Request berhasil disetujui. Tim telah ditambahkan ke tournament dan statistik awal telah dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyetujui request: ' . $e->getMessage());
        }
    }

    public function rejectRequest($id)
    {
        $user = Auth::user();

        if ($user->role != 1) {
            return redirect('/')->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        $request = TournamentRequest::findOrFail($id);
        $request->update(['status' => 'rejected']);

        return redirect()->back()->with('error', 'Request ditolak.');
    }
}
