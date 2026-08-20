<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rappels_paiement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');
            $table->foreignId('frais_id')->nullable()->constrained('frais')->onDelete('set null');
            $table->decimal('montant_du', 10, 2)->default(0);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->decimal('solde', 10, 2)->default(0);
            $table->string('type_rappel'); // hebdomadaire, mensuel, trimestriel, semestriel
            $table->string('statut')->default('en_attente'); // en_attente, envoyé, échoué
            $table->string('email_destinataire')->nullable();
            $table->string('sms_destinataire')->nullable();
            $table->boolean('email_envoye')->default(false);
            $table->boolean('sms_envoye')->default(false);
            $table->text('message_erreur')->nullable();
            $table->timestamp('date_envoi')->nullable();
            $table->timestamps();

            $table->index(['ecole_id', 'statut']);
            $table->index(['inscription_id', 'date_envoi']);
        });

        // Table de configuration des rappels
        Schema::create('config_rappels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->unique()->constrained('ecoles')->onDelete('cascade');
            $table->boolean('actif')->default(true);
            $table->string('frequence')->default('hebdomadaire'); // hebdomadaire, mensuel, trimestriel, semestriel
            $table->string('jour_envoi')->default('monday'); // lundi-dimanche
            $table->integer('jour_du_mois')->nullable()->default(1); // pour mensuel
            $table->integer('heure_envoi')->default(8); // 8h du matin
            $table->boolean('email_actif')->default(true);
            $table->boolean('sms_actif')->default(false);
            $table->text('message_personnalise')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config_rappels');
        Schema::dropIfExists('rappels_paiement');
    }
};
