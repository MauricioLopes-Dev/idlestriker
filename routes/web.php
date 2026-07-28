<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;

Route::get('/', function () {
    if (session()->pull('game_launch_pending', false)) {
        return app(GameController::class)->index();
    }

    return view('welcome');
});
Route::middleware('auth')->group(function () {
    Route::get('/launch', function () {
        session()->put('game_launch_pending', true);

        return redirect('/game');
    })->name('game.launch');

    Route::get('/game', function () {
        session()->put('game_launch_pending', true);

        return app(GameController::class)->index();
    })->name('game.index');

    Route::get('/game/opponent', [GameController::class, 'findOpponent']);
    Route::post('/game/match-finish', [GameController::class, 'finishMatch']);
    Route::post('/game/player/setup', [GameController::class, 'updateTacticalSetup']);
    Route::post('/game/player/scout', [GameController::class, 'scoutPlayer']);
    Route::post('/game/player/swap', [GameController::class, 'swapPlayer']);
    Route::post('/game/case/open', [GameController::class, 'openCase']);
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');