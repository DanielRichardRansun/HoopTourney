<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    // Menyimpan pemain baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'jersey_number' => 'required|integer',
            'position' => 'required|string|max:255',
            'team_id' => 'required|exists:teams,id',
        ]);

        Player::create([
            'name' => $request->name,
            'jersey_number' => $request->jersey_number,
            'position' => $request->position,
            'teams_id' => $request->team_id,
        ]);

        return redirect()->back()->with('success', 'Pemain berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'jersey_number' => 'required|integer',
            'position' => 'required|string|max:255',
        ]);

        $player = Player::findOrFail($id);
        $player->update([
            'name' => $request->name,
            'jersey_number' => $request->jersey_number,
            'position' => $request->position
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data pemain berhasil diperbarui'
        ]);
    }

    // Menghapus pemain
    public function destroy($id)
    {
        try {
            $player = Player::findOrFail($id);
            $player->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pemain berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pemain: ' . $e->getMessage()
            ], 500);
        }
    }
}
