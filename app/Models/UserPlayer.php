<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPlayer extends Model
{
    protected $fillable = [
        'user_id',
        'player_catalog_id',
        'slot_index',
        'tactical_setups'
    ];

    // Converte automaticamente a coluna JSON do banco para Array no PHP
    protected $casts = [
        'tactical_setups' => 'array',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(PlayerCatalog::class, 'player_catalog_id');
    }
}