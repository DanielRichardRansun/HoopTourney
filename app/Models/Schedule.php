<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MatchResults;

class Schedule extends Model
{
    protected $fillable = [
        'team1_id',
        'team2_id',
        'date',
        'location',
        'tournaments_id',
        'status',
        'round',
    ];

    // Pastikan ada relasi dengan tim 1
    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    // Pastikan ada relasi dengan tim 2
    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function matchResult()
    {
        return $this->hasOne(MatchResult::class, 'schedules_id');
    }
}
