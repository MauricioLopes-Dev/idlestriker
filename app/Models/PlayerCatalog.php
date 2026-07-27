<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerCatalog extends Model
{
    protected $fillable = [
        'name',
        'role',
        'base_ovr',
        'tier',
        'is_default_recruit'
    ];
}