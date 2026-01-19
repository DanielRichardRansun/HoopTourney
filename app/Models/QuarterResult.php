<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuarterResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_results_id',
        'quarter_number',
        'team1_score',
        'team2_score',
    ];

    public function matchResult()
    {
        return $this->belongsTo(MatchResult::class, 'match_results_id');
    }
}
