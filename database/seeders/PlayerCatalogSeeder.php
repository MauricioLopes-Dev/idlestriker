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
            ],
            [
                'name' => 'Biace',
                'role' => 'AWPer • Sniper',
                'base_ovr' => 58,
                'tier' => 'Tier 4 (Base)',
                'is_default_recruit' => true,
            ],
            [
                'name' => 'Maciesk',
                'role' => 'Entry Fragger',
                'base_ovr' => 56,
                'tier' => 'Tier 4 (Base)',
                'is_default_recruit' => true,
            ],
            [
                'name' => 'Leonardo',
                'role' => 'Suporte Tático',
                'base_ovr' => 53,
                'tier' => 'Tier 4 (Base)',
                'is_default_recruit' => true,
            ],
            [
                'name' => 'Cuty',
                'role' => 'Lurker • Flanco',
                'base_ovr' => 53,
                'tier' => 'Tier 4 (Base)',
                'is_default_recruit' => true,
            ],

            // --- TIER 4 (Base / Amador) ---
            ['name' => 'Mauis', 'role' => 'Entry Fragger', 'base_ovr' => 59, 'tier' => 'Tier 4 (Base)', 'is_default_recruit' => false],
            ['name' => 'Taco', 'role' => 'Lurker • Flanco', 'base_ovr' => 61, 'tier' => 'Tier 4 (Base)', 'is_default_recruit' => false],
            ['name' => 'Mikail', 'role' => 'Suporte Tático', 'base_ovr' => 60, 'tier' => 'Tier 4 (Base)', 'is_default_recruit' => false],
            ['name' => 'Brehze', 'role' => 'IGL • Capitão', 'base_ovr' => 62, 'tier' => 'Tier 4 (Base)', 'is_default_recruit' => false],
            ['name' => 'Daps', 'role' => 'Suporte Tático', 'base_ovr' => 63, 'tier' => 'Tier 4 (Base)', 'is_default_recruit' => false],

            // --- TIER 3 (Challenger) ---
            ['name' => 'karrigan', 'role' => 'IGL • Capitão', 'base_ovr' => 71, 'tier' => 'Tier 3 (Challenger)', 'is_default_recruit' => false],
            ['name' => 'JKS', 'role' => 'Entry Fragger', 'base_ovr' => 74, 'tier' => 'Tier 3 (Challenger)', 'is_default_recruit' => false],
            ['name' => 'broky', 'role' => 'AWPer • Sniper', 'base_ovr' => 76, 'tier' => 'Tier 3 (Challenger)', 'is_default_recruit' => false],
            ['name' => 'mopoz', 'role' => 'Lurker • Flanco', 'base_ovr' => 73, 'tier' => 'Tier 3 (Challenger)', 'is_default_recruit' => false],
            ['name' => 'Xantares', 'role' => 'IGL • Capitão', 'base_ovr' => 77, 'tier' => 'Tier 3 (Challenger)', 'is_default_recruit' => false],
            ['name' => 'sh1ro', 'role' => 'AWPer • Sniper', 'base_ovr' => 79, 'tier' => 'Tier 3 (Challenger)', 'is_default_recruit' => false],

            // --- TIER 2 (Pro League) ---
            ['name' => 'KSCERATO', 'role' => 'Lurker • Flanco', 'base_ovr' => 86, 'tier' => 'Tier 2 (Pro League)', 'is_default_recruit' => false],
            ['name' => 'NiKo', 'role' => 'Entry Fragger', 'base_ovr' => 88, 'tier' => 'Tier 2 (Pro League)', 'is_default_recruit' => false],
            ['name' => 'ropz', 'role' => 'AWPer • Sniper', 'base_ovr' => 89, 'tier' => 'Tier 2 (Pro League)', 'is_default_recruit' => false],
            ['name' => 'ZywOo', 'role' => 'AWPer • Sniper', 'base_ovr' => 91, 'tier' => 'Tier 2 (Pro League)', 'is_default_recruit' => false],
            ['name' => 'm0NESY', 'role' => 'Entry Fragger', 'base_ovr' => 90, 'tier' => 'Tier 2 (Pro League)', 'is_default_recruit' => false],
            ['name' => 'device', 'role' => 'IGL • Capitão', 'base_ovr' => 92, 'tier' => 'Tier 2 (Pro League)', 'is_default_recruit' => false],
            ['name' => 'twistzz', 'role' => 'Lurker • Flanco', 'base_ovr' => 87, 'tier' => 'Tier 2 (Pro League)', 'is_default_recruit' => false],
            ['name' => 'Magisk', 'role' => 'Suporte Tático', 'base_ovr' => 88, 'tier' => 'Tier 2 (Pro League)', 'is_default_recruit' => false],

            // --- TIER 1 (Lenda) ---
            ['name' => 'FalleN', 'role' => 'IGL • Capitão', 'base_ovr' => 94, 'tier' => 'Tier 1 (Lenda)', 'is_default_recruit' => false],
            ['name' => 's1mple', 'role' => 'AWPer • Sniper', 'base_ovr' => 96, 'tier' => 'Tier 1 (Lenda)', 'is_default_recruit' => false],
            ['name' => 'coldzera', 'role' => 'Entry Fragger', 'base_ovr' => 95, 'tier' => 'Tier 1 (Lenda)', 'is_default_recruit' => false],
            ['name' => 'dev1ce', 'role' => 'AWPer • Sniper', 'base_ovr' => 97, 'tier' => 'Tier 1 (Lenda)', 'is_default_recruit' => false],
            ['name' => 'Stewie2K', 'role' => 'Lurker • Flanco', 'base_ovr' => 93, 'tier' => 'Tier 1 (Lenda)', 'is_default_recruit' => false],
            ['name' => 'EliGE', 'role' => 'Suporte Tático', 'base_ovr' => 94, 'tier' => 'Tier 1 (Lenda)', 'is_default_recruit' => false],
            ['name' => 'Niko', 'role' => 'Entry Fragger', 'base_ovr' => 95, 'tier' => 'Tier 1 (Lenda)', 'is_default_recruit' => false],
        ];

        foreach ($players as $player) {
            $payload = array_merge($player, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('player_catalogs')->updateOrInsert(
                ['name' => $player['name']],
                $payload
            );
        }
    }
}