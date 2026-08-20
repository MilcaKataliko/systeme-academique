<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ajoute le pourcentage de présence (0-100) de l'élève pour le cours.
     * Le bonus de présence est calculé à partir de cette valeur :
     *   - Présence à 100%  => bonus de 5%
     *   - Sinon            => bonus de 1%
     */
    public function up(): void
    {
        Schema::table('cotes', function (Blueprint $table) {
            $table->decimal('pourcentage_presence', 5, 2)->nullable()->after('examen_s2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotes', function (Blueprint $table) {
            $table->dropColumn('pourcentage_presence');
        });
    }
};
