<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use App\Models\PlayerCatalog;
use App\Models\UserPlayer;
use App\Models\UserSkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
   public function index()
    {
        // Pega sempre o primeiro usuário da base (ou cria se não existir)
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Operador Convidado',
                'email' => 'teste@idlestrike.com',
                'password' => bcrypt('secret123')
            ]);
        }

        $team = Team::firstOrCreate(
            ['user_id' => $user->id],
            ['team_name' => 'Gaderna Gaming', 'money' => 100.00, 'elo' => 1000]
        );

        // Se não houver nenhum titular para este usuário, injeta os recrutas imediatamente
        if (UserPlayer::where('user_id', $user->id)->whereNotNull('slot_index')->count() === 0) {
            UserPlayer::where('user_id', $user->id)->delete();
            $this->seedInitialTeam($user->id);
        }

        $roster = UserPlayer::with('catalog')
            ->where('user_id', $user->id)
            ->whereNotNull('slot_index')
            ->orderBy('slot_index')
            ->get();

        $bench = UserPlayer::with('catalog')
            ->where('user_id', $user->id)
            ->whereNull('slot_index')
            ->get();

        $inventory = UserSkin::where('user_id', $user->id)->orderByDesc('id')->get();

        return view('game.index', [
            'user' => $user,
            'team' => $team,
            'roster' => $roster,
            'bench' => $bench,
            'inventory' => $inventory
        ]);
    }

    /**
     * Substitui um jogador titular por um reserva no MySQL.
     */
    public function swapPlayer(Request $request)
    {
        $validated = $request->validate([
            'starter_id' => 'required|integer|exists:user_players,id',
            'bench_id' => 'required|integer|exists:user_players,id',
        ]);

        $user = User::firstOrCreate(['email' => 'teste@idlestrike.com']);

        $starter = UserPlayer::where('id', $validated['starter_id'])->where('user_id', $user->id)->firstOrFail();
        $bench = UserPlayer::where('id', $validated['bench_id'])->where('user_id', $user->id)->firstOrFail();

        $currentSlot = $starter->slot_index;

        DB::transaction(function () use ($starter, $bench, $currentSlot) {
            $starter->slot_index = null;
            $starter->save();

            $bench->slot_index = $currentSlot;
            $bench->save();
        });

        $updatedRoster = UserPlayer::with('catalog')->where('user_id', $user->id)->whereNotNull('slot_index')->orderBy('slot_index')->get();
        $updatedBench = UserPlayer::with('catalog')->where('user_id', $user->id)->whereNull('slot_index')->get();

        return response()->json([
            'success' => true,
            'roster' => $updatedRoster,
            'bench' => $updatedBench
        ]);
    }

    private function seedInitialTeam($userId): void
    {
        $recruits = PlayerCatalog::where('is_default_recruit', true)->get();

        $defaultSetups = [
            'Gaderna'  => ['pistol' => 'Glock-18', 'eco' => 'MAC-10', 'full' => 'AK-47'],
            'Biace'    => ['pistol' => 'Desert Eagle', 'eco' => 'SSG 08 (Scout)', 'full' => 'AWP'],
            'Maciesk'  => ['pistol' => 'Dual Berettas', 'eco' => 'MP9', 'full' => 'AK-47'],
            'Leonardo' => ['pistol' => 'P250', 'eco' => 'Nova', 'full' => 'M4A4'],
            'Cuty'     => ['pistol' => 'USP-S', 'eco' => 'UMP-45', 'full' => 'Galil AR'],
        ];

        DB::transaction(function () use ($userId, $recruits, $defaultSetups) {
            foreach ($recruits as $index => $recruit) {
                $weapons = $defaultSetups[$recruit->name] ?? ['pistol' => 'Glock-18', 'eco' => 'MP9', 'full' => 'M4A4'];

                UserPlayer::create([
                    'user_id' => $userId,
                    'player_catalog_id' => $recruit->id,
                    'slot_index' => $index,
                    'tactical_setups' => [
                        'pistol' => ['weapon' => $weapons['pistol'], 'user_skin_id' => null, 'buff' => 0.0, 'mult' => 1.0],
                        'eco'    => ['weapon' => $weapons['eco'],    'user_skin_id' => null, 'buff' => 0.0, 'mult' => 1.3],
                        'full'   => ['weapon' => $weapons['full'],   'user_skin_id' => null, 'buff' => 0.0, 'mult' => 2.0],
                    ]
                ]);
            }
        });
    }

    public function finishMatch(Request $request)
    {
        $validated = $request->validate([
            'my_score' => 'required|integer|min:0|max:15',
            'enemy_score' => 'required|integer|min:0|max:15',
        ]);

        $user = User::firstOrCreate(['email' => 'teste@idlestrike.com']);
        $team = Team::where('user_id', $user->id)->firstOrFail();

        $team->matches_played += 1;

        if ($validated['my_score'] > $validated['enemy_score']) {
            $team->wins += 1;
            $team->elo += 25;
            $prize = 200 + ($validated['my_score'] * 20);
        } else {
            $team->losses += 1;
            $team->elo = max(0, $team->elo - 12);
            $prize = 70 + ($validated['my_score'] * 5);
        }

        $team->money += $prize;
        $team->save();

        return response()->json([
            'success' => true,
            'new_money' => $team->money,
            'new_elo' => $team->elo,
            'matches_played' => $team->matches_played,
            'wins' => $team->wins,
            'losses' => $team->losses,
            'prize_earned' => $prize,
            'is_win' => ($validated['my_score'] > $validated['enemy_score'])
        ]);
    }

    public function updateTacticalSetup(Request $request)
    {
        $validated = $request->validate([
            'user_player_id' => 'required|integer|exists:user_players,id',
            'pistol_weapon' => 'required|string|max:50',
            'eco_weapon' => 'required|string|max:50',
            'full_weapon' => 'required|string|max:50',
        ]);

        $user = User::firstOrCreate(['email' => 'teste@idlestrike.com']);

        $userPlayer = UserPlayer::where('id', $validated['user_player_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $currentSetups = $userPlayer->tactical_setups;
        
        $currentSetups['pistol']['weapon'] = $validated['pistol_weapon'];
        $currentSetups['eco']['weapon'] = $validated['eco_weapon'];
        $currentSetups['full']['weapon'] = $validated['full_weapon'];

        $userPlayer->tactical_setups = $currentSetups;
        $userPlayer->save();

        return response()->json([
            'success' => true,
            'updated_setups' => $userPlayer->tactical_setups,
            'player_name' => $userPlayer->catalog->name
        ]);
    }

    public function scoutPlayer(Request $request)
    {
        $validated = $request->validate([
            'scout_type' => 'required|string|in:local,international,major'
        ]);

        $user = User::firstOrCreate(['email' => 'teste@idlestrike.com']);
        $team = Team::where('user_id', $user->id)->firstOrFail();

        $scoutConfigs = [
            'local' => [
                'cost' => 250.00,
                'weights' => ['Tier 4 (Base)' => 85, 'Tier 3 (Challenger)' => 14, 'Tier 2 (Pro League)' => 1, 'Tier 1 (Lenda)' => 0]
            ],
            'international' => [
                'cost' => 2500.00,
                'weights' => ['Tier 4 (Base)' => 0, 'Tier 3 (Challenger)' => 75, 'Tier 2 (Pro League)' => 23, 'Tier 1 (Lenda)' => 2]
            ],
            'major' => [
                'cost' => 15000.00,
                'weights' => ['Tier 4 (Base)' => 0, 'Tier 3 (Challenger)' => 0, 'Tier 2 (Pro League)' => 85, 'Tier 1 (Lenda)' => 15]
            ]
        ];

        $config = $scoutConfigs[$validated['scout_type']];

        if ($team->money < $config['cost']) {
            return response()->json(['success' => false, 'error' => 'Saldo insuficiente no cofre do clube!'], 400);
        }

        $rand = mt_rand(1, 100);
        $cumulative = 0;
        $selectedTier = 'Tier 4 (Base)';

        foreach ($config['weights'] as $tier => $weight) {
            if ($weight === 0) continue;
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                $selectedTier = $tier;
                break;
            }
        }

        $pulledCatalog = PlayerCatalog::where('tier', $selectedTier)->inRandomOrder()->first();
        if (!$pulledCatalog) {
            $pulledCatalog = PlayerCatalog::where('tier', 'Tier 4 (Base)')->inRandomOrder()->first();
        }

        DB::transaction(function () use ($team, $config, $pulledCatalog, $user) {
            $team->money -= $config['cost'];
            $team->save();

            UserPlayer::create([
                'user_id' => $user->id,
                'player_catalog_id' => $pulledCatalog->id,
                'slot_index' => null,
                'tactical_setups' => [
                    'pistol' => ['weapon' => 'Glock-18', 'user_skin_id' => null, 'buff' => 0.0, 'mult' => 1.0],
                    'eco'    => ['weapon' => 'MAC-10',   'user_skin_id' => null, 'buff' => 0.0, 'mult' => 1.3],
                    'full'   => ['weapon' => 'AK-47',    'user_skin_id' => null, 'buff' => 0.0, 'mult' => 2.0],
                ]
            ]);
        });

        return response()->json([
            'success' => true,
            'new_money' => $team->money,
            'pulled_player' => [
                'name' => $pulledCatalog->name,
                'role' => $pulledCatalog->role,
                'baseOvr' => $pulledCatalog->base_ovr,
                'tier' => $pulledCatalog->tier
            ]
        ]);
    }

    public function openCase(Request $request)
    {
        $validated = $request->validate([
            'case_type' => 'required|string|in:basic,operation,covert'
        ]);

        $user = User::firstOrCreate(['email' => 'teste@idlestrike.com']);
        $team = Team::where('user_id', $user->id)->firstOrFail();

        $caseConfigs = [
            'basic' => [
                'cost' => 500.00,
                'weights' => ['Mil-Spec' => 79, 'Restricted' => 17, 'Classified' => 3, 'Covert' => 1, 'Rare Special' => 0]
            ],
            'operation' => [
                'cost' => 3500.00,
                'weights' => ['Mil-Spec' => 25, 'Restricted' => 50, 'Classified' => 19, 'Covert' => 5, 'Rare Special' => 1]
            ],
            'covert' => [
                'cost' => 25000.00,
                'weights' => ['Mil-Spec' => 0, 'Restricted' => 15, 'Classified' => 60, 'Covert' => 20, 'Rare Special' => 5]
            ]
        ];

        $config = $caseConfigs[$validated['case_type']];

        if ($team->money < $config['cost']) {
            return response()->json(['success' => false, 'error' => 'Saldo insuficiente para abrir esta caixa!'], 400);
        }

        $rand = mt_rand(1, 100);
        $cumulative = 0;
        $selectedRarity = 'Mil-Spec';

        foreach ($config['weights'] as $rarity => $weight) {
            if ($weight === 0) continue;
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                $selectedRarity = $rarity;
                break;
            }
        }

        $skinsPool = [
            'Mil-Spec' => [
                ['weapon' => 'Glock-18', 'skin' => 'Candy Apple', 'buff' => 0.15],
                ['weapon' => 'USP-S', 'skin' => 'Torque', 'buff' => 0.18],
                ['weapon' => 'MAC-10', 'skin' => 'Silver', 'buff' => 0.20],
                ['weapon' => 'P250', 'skin' => 'Valence', 'buff' => 0.15],
                ['weapon' => 'MP9', 'skin' => 'Bioleak', 'buff' => 0.22],
            ],
            'Restricted' => [
                ['weapon' => 'AK-47', 'skin' => 'Elite Build', 'buff' => 0.35],
                ['weapon' => 'M4A1-S', 'skin' => 'Basilisk', 'buff' => 0.40],
                ['weapon' => 'Desert Eagle', 'skin' => 'Conspiracy', 'buff' => 0.45],
                ['weapon' => 'AWP', 'skin' => 'Fever Dream', 'buff' => 0.50],
                ['weapon' => 'Galil AR', 'skin' => 'Eco', 'buff' => 0.38],
            ],
            'Classified' => [
                ['weapon' => 'AK-47', 'skin' => 'Redline', 'buff' => 0.75],
                ['weapon' => 'M4A4', 'skin' => 'Desolate Space', 'buff' => 0.80],
                ['weapon' => 'AWP', 'skin' => 'Redline', 'buff' => 0.85],
                ['weapon' => 'USP-S', 'skin' => 'Cortex', 'buff' => 0.70],
                ['weapon' => 'Desert Eagle', 'skin' => 'Kumicho Dragon', 'buff' => 0.90],
            ],
            'Covert' => [
                ['weapon' => 'AK-47', 'skin' => 'Asiimov', 'buff' => 1.50],
                ['weapon' => 'M4A4', 'skin' => 'Neo-Noir', 'buff' => 1.60],
                ['weapon' => 'AWP', 'skin' => 'Asiimov', 'buff' => 2.00],
                ['weapon' => 'Desert Eagle', 'skin' => 'Printstream', 'buff' => 1.80],
                ['weapon' => 'AK-47', 'skin' => 'Vulcan', 'buff' => 1.75],
            ],
            'Rare Special' => [
                ['weapon' => 'AWP', 'skin' => 'Dragon Lore 🐉', 'buff' => 4.50],
                ['weapon' => 'Faca Borboleta', 'skin' => 'Fade ✨', 'buff' => 4.00],
                ['weapon' => 'AK-47', 'skin' => 'Case Hardened (Blue Gem)', 'buff' => 3.80],
                ['weapon' => 'M4A4', 'skin' => 'Howl 🔥', 'buff' => 4.20],
                ['weapon' => 'Luvas Esportivas', 'skin' => 'Vice 🧤', 'buff`' => 3.50],
            ]
        ];

        $pool = $skinsPool[$selectedRarity];
        $selectedSkin = $pool[array_rand($pool)];

        $floatValue = mt_rand(1, 9999) / 10000;
        $floatMult = 1.0;
        if ($floatValue <= 0.07) $floatMult = 1.25;
        elseif ($floatValue <= 0.15) $floatMult = 1.15;
        elseif ($floatValue <= 0.38) $floatMult = 1.00;
        elseif ($floatValue <= 0.45) $floatMult = 0.90;
        else $floatMult = 0.80;

        $finalBuff = round($selectedSkin['buff'] * $floatMult, 2);
        $isStattrak = (mt_rand(1, 100) <= 10);

        $droppedSkin = null;
        DB::transaction(function () use ($team, $selectedRarity, $selectedSkin, $floatValue, $finalBuff, $isStattrak, &$droppedSkin, $user) {
            $team->money -= 500.00; // Será ajustado conforme o custo real se necessário
            $team->save();

            $droppedSkin = UserSkin::create([
                'user_id' => $user->id,
                'weapon_name' => $selectedSkin['weapon'],
                'skin_name' => $selectedSkin['skin'],
                'rarity' => $selectedRarity,
                'float_value' => $floatValue,
                'buff_multiplier' => $finalBuff,
                'is_stattrak' => $isStattrak,
                'stattrak_kills' => 0
            ]);
        });

        return response()->json([
            'success' => true,
            'new_money' => $team->money,
            'dropped_skin' => $droppedSkin
        ]);
    }
}