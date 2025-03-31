<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchResult extends Model
{
    use HasFactory;

    protected $table = 'match_results';

    protected $fillable = [
        'team1_score',
        'team2_score',
        'winning_team_id',
        'losing_team_id',
        'schedules_id',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedules_id');
    }

    public function winningTeam()
    {
        return $this->belongsTo(Team::class, 'winning_team_id');
    }

    public function losingTeam()
    {
        return $this->belongsTo(Team::class, 'losing_team_id');
    }
}
