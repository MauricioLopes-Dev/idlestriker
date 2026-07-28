<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSkin extends Model
{
    protected $fillable = [
        'user_id',
        'weapon_name',
        'skin_name',
        'rarity',
        'float_value',
        'buff_multiplier',
        'is_stattrak',
        'stattrak_kills',
    ];
}
