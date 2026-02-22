<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'name',
        'jersey_number',
        'position',
        'teams_id',
        'photo',
    ];
    // Relasi ke Tournament
    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournaments_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'teams_id');
    }

    // Relasi ke PlayerStat
    public function playerStats()
    {
        return $this->hasMany(PlayerStat::class, 'players_id');
    }
}
