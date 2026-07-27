<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: Gaderna, Biace, FalleN
            $table->string('role'); // Ex: IGL • Capitão, AWPer • Sniper
            $table->integer('base_ovr'); // Ex: 55 até 99
            
            // Tiers: Tier 4 (Base), Tier 3 (Challenger), Tier 2 (Pro League), Tier 1 (Lenda)
            $table->string('tier')->default('Tier 4 (Base)');
            
            // Identifica se faz parte do time inicial gratuito
            $table->boolean('is_default_recruit')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_catalogs');
    }
};