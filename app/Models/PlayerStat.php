<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerStat extends Model
{
    protected $fillable = [
        'players_id',
        'match_results_id',
        'quarter_number',
        'per',
        'point',
        'fgm',
        'fga',
        'fta',
        'ftm',
        'orb',
        'drb',
        'stl',
        'ast',
        'blk',
        'pf',
        'to'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'players_id');
    }
}
