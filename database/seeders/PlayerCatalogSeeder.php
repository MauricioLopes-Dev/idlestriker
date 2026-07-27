<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $players = [
            // --- TIME INICIAL PADRÃO (Tier 4 • Recrutas Base) ---
            [
                'name' => 'Gaderna',
                'role' => 'IGL • Capitão',
                'base_ovr' => 55,
                'tier' => 'Tier 4 (Base)',
                'is_default_recruit' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Biace',
                'role' => 'AWPer • Sniper',
                'base_ovr' => 58,
                'tier' => 'Tier 4 (Base)',
                'is_default_recruit' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maciesk',
                'role' => 'Entry Fragger',
                'base_ovr' => 56,
                'tier' => 'Tier 4 (Base)',
                'is_default_recruit' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leonardo',
                'role' => 'Suporte Tático',
                'base_ovr' => 53,
                'tier' => 'Tier 4 (Base)',
                'is_default_recruit' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cuty',
                'role' => 'Lurker • Flanco',
                'base_ovr' => 53,
                'tier' => 'Tier 4 (Base)',
                'is_default_recruit' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- ASTROS PARA O SISTEMA DE OLHEIROS (Gacha) ---
            [
                'name' => 'KSCERATO',
                'role' => 'Lurker • Flanco',
                'base_ovr' => 86,
                'tier' => 'Tier 2 (Pro League)',
                'is_default_recruit' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'FalleN',
                'role' => 'IGL • Capitão',
                'base_ovr' => 94,
                'tier' => 'Tier 1 (Lenda)',
                'is_default_recruit' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 's1mple',
                'role' => 'AWPer • Sniper',
                'base_ovr' => 96,
                'tier' => 'Tier 1 (Lenda)',
                'is_default_recruit' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('player_catalogs')->insert($players);
    }
}