<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TournamentRequest;
use App\Models\Tournament;
use App\Models\Team;
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

        $query = TournamentRequest::with(['user', 'team', 'tournament'])
            ->latest();

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $requests = $query->get();

        return view('admin.tournament_requests', compact('requests'));
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

            DB::commit();
            return redirect()->back()->with('success', 'Request berhasil disetujui dan tim telah ditambahkan ke tournament.');
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
