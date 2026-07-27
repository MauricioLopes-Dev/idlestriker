<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_skins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('weapon_name'); // Ex: AWP, AK-47, Glock-18
            $table->string('skin_name'); // Ex: Dragon Lore, Redline, Padrão
            $table->string('rarity')->default('Mil-Spec'); // Mil-Spec, Restricted, Covert, etc.
            
            // Float de desgaste de 0.0000 a 1.0000
            $table->decimal('float_value', 5, 4)->default(0.1500);
            
            // Multiplicador de bônus (Ex: 0.50 para +50%, 2.00 para +200%)
            $table->decimal('buff_multiplier', 5, 2)->default(0.00);
            
            $table->boolean('is_stattrak')->default(false);
            $table->integer('stattrak_kills')->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_skins');
    }
};