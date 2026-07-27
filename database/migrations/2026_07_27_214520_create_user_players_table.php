<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_catalog_id')->constrained('player_catalogs')->onDelete('cascade');
            
            // Posição no time titular: 0 a 4 (ou null se o atleta estiver na reserva/banco)
            $table->integer('slot_index')->nullable();
            
            // Configuração dos armamentos (Pistol, Eco, Full Buy) gravada em formato JSON
            $table->json('tactical_setups')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_players');
    }
};