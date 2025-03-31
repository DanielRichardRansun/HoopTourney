<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchResults extends Model
{
    protected $fillable = [
        'schedules_id',
        'team1_score',
        'team2_score',
        'winning_team_id',
        'losing_team_id',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedules_id');
    }
}
