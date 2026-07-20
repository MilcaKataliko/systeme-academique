<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('annees', function (Blueprint $table) {
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');        });

        Schema::table('options', function (Blueprint $table) {
            // Remarque : ta clé primaire personnalisée s'appelle idOption, la nouvelle colonne se place après elle
            $table->foreignId('ecole_id')->after('idOption')->constrained('ecoles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('annees', function (Blueprint $table) {
            $table->dropForeign(['ecole_id']);
            $table->dropColumn('ecole_id');
        });

        Schema::table('options', function (Blueprint $table) {
            $table->dropForeign(['ecole_id']);
            $table->dropColumn('ecole_id');
        });
    }
};