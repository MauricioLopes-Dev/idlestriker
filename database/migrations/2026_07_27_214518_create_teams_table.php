<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            // Relacionamento com a tabela de usuários nativa do Laravel
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('team_name')->default('Recrutas Gaming');
            $table->decimal('money', 15, 2)->default(100.00); // Dinheiro do clube
            $table->integer('elo')->default(1000); // Rating competitivo
            
            // Estatísticas globais
            $table->integer('matches_played')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};