<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController; // 1. Importe o controlador no topo

// 2. Aponta a página inicial diretamente para o método index do controlador
Route::get('/', [GameController::class, 'index']);
Route::post('/game/match-finish', [GameController::class, 'finishMatch']);
Route::post('/game/player/setup', [GameController::class, 'updateTacticalSetup']);
Route::post('/game/player/scout', [GameController::class, 'scoutPlayer']);
Route::post('/game/player/swap', [GameController::class, 'swapPlayer']);
Route::post('/game/case/open', [GameController::class, 'openCase']);