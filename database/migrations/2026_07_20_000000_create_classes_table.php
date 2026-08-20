<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('classes', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('option_id')->nullable()->constrained('options', 'idOption')->onDelete('set null');
        //     $table->string('nom_classe'); // ex: 1ère Commerciale, 2ème Chimie-Bio
        //     $table->integer('niveau'); // 1 à 6 (année d'étude)
        //     $table->string('section')->nullable(); // ex: Scientifique, Littéraire, Technique
        //     $table->timestamps();
        // });
    }

    public function down(): void
    {
        // Schema::dropIfExists('classes');
    }
};
