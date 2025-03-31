<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamTournament extends Model
{
    use HasFactory;

    // Nama tabel yang terkait dengan model ini
    protected $table = 'team_tournament';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'team_id',
        'tournament_id',
        'created_at',
        'updated_at',
    ];

    // Relasi ke model Team
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    // Relasi ke model Tournament
    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }
}
