<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GachaCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_case_gacha_returns_pool_summary_with_skin_options(): void
    {
        $user = User::factory()->create();
        Team::where('user_id', $user->id)->update(['money' => 100000]);

        $this->actingAs($user);

        $response = $this->postJson('/game/case/open', ['case_type' => 'basic']);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'dropped_skin' => ['rarity', 'weapon_name', 'skin_name'],
            'pool_summary' => [
                'basic' => [
                    'Mil-Spec' => ['options' => []],
                    'Restricted' => ['options' => []],
                    'Classified' => ['options' => []],
                    'Covert' => ['options' => []],
                    'Rare Special' => ['options' => []],
                ],
            ],
        ]);

        $this->assertNotEmpty($response->json('pool_summary.basic.Mil-Spec.options'));
    }
}
