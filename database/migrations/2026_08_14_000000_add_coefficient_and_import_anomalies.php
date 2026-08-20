<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedSmallInteger('coefficient')->default(1)->after('maxima_examen');
        });

        Schema::create('bulletin_import_anomalies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->cascadeOnDelete();
            $table->string('matricule');
            $table->string('code_cours');
            $table->string('champ');
            $table->decimal('note', 8, 2)->nullable();
            $table->string('motif');
            $table->unsignedInteger('ligne_source')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletin_import_anomalies');
        Schema::table('plans', fn (Blueprint $table) => $table->dropColumn('coefficient'));
    }
};
