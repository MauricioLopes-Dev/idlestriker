<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDefaultTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_receives_default_team_when_created(): void
    {
        $user = User::create([
            'name' => 'Novo Jogador',
            'email' => 'novo@exemplo.com',
            'password' => 'secret123',
        ]);

        $this->assertDatabaseHas('teams', [
            'user_id' => $user->id,
            'team_name' => 'Gaderna Gaming',
        ]);

        $this->assertNotNull($user->team);
        $this->assertSame(100.00, (float) $user->team->money);
    }

    public function test_session_user_is_used_for_the_game_state(): void
    {
        $primaryUser = User::create([
            'name' => 'Usuário Primário',
            'email' => 'primario@exemplo.com',
            'password' => 'secret123',
        ]);

        $sessionUser = User::create([
            'name' => 'Usuário da Sessão',
            'email' => 'sessao@exemplo.com',
            'password' => 'secret123',
        ]);

        $primaryUser->team()->update(['team_name' => 'Time Primário']);
        $sessionUser->team()->update(['team_name' => 'Time da Sessão']);

        $response = $this->actingAs($sessionUser)->withSession([
            'guest_user_id' => $sessionUser->id,
            'game_launch_pending' => true,
        ])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Time da Sessão');
        $response->assertSee('Gaderna');
        $response->assertSee('Biace');
        $response->assertDontSee('Time Primário');
    }

    public function test_user_can_register_and_become_authenticated(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jogador Novo',
            'email' => 'cadastro@exemplo.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertRedirect('/');
        $user = User::where('email', 'cadastro@exemplo.com')->first();

        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('teams', [
            'user_id' => $user->id,
            'team_name' => 'Gaderna Gaming',
        ]);
    }

    public function test_registered_user_receives_initial_roster_after_registration(): void
    {
        $response = $this->followingRedirects()->post('/register', [
            'name' => 'Novo Time',
            'email' => 'novo-time@exemplo.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Gaderna');
        $response->assertSee('Biace');
        $response->assertSee('Maciesk');

        $user = User::where('email', 'novo-time@exemplo.com')->first();
        $this->assertNotNull($user);
        $this->assertDatabaseHas('user_players', [
            'user_id' => $user->id,
            'slot_index' => 0,
        ]);
        $this->assertDatabaseCount('user_players', 5);
    }

    public function test_opponent_matchmaking_uses_other_registered_user_if_available(): void
    {
        $primaryUser = User::create([
            'name' => 'Usuário Primário',
            'email' => 'primario@exemplo.com',
            'password' => 'secret123',
        ]);

        $opponentUser = User::create([
            'name' => 'Oponente',
            'email' => 'oponente@exemplo.com',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($primaryUser)->get('/game/opponent');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['success', 'team_name', 'roster']);
    }

    public function test_game_requires_authentication(): void
    {
        $response = $this->get('/game');

        $response->assertRedirectContains('/login');
    }
}
