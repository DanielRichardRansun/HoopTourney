<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $table = 'tournaments';

    protected $fillable = [
        'name',
        'organizer',
        'description',
        'start_date',
        'end_date',
        'status',
        'users_id'
    ];
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_tournament', 'tournament_id', 'team_id')->withTimestamps();
    }
}
