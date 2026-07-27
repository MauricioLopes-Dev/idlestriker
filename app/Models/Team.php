<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends Model
{
    protected $fillable = [
        'user_id',
        'team_name',
        'money',
        'elo',
        'matches_played',
        'wins',
        'losses'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}