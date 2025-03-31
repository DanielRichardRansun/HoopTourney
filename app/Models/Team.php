<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Team extends Model
{
    protected $fillable = ['name', 'coach', 'manager'];
    public function teamTournament()
    {
        return $this->hasMany(TeamTournament::class);
    }
    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'team_tournament', 'team_id', 'tournament_id')->withTimestamps();
    }
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
    public function players()
    {
        return $this->hasMany(Player::class, 'teams_id');
    }
}
