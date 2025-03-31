<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamStat extends Model
{
    protected $table = 'team_stats';
    protected $fillable = ['wins', 'losses', 'teams_id', 'tournaments_id'];

    // Relasi ke tim
    public function team()
    {
        return $this->belongsTo(Team::class, 'teams_id');
    }

    // Relasi ke turnamen
    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournaments_id');
    }
}
