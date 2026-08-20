<?php

namespace App\Console\Commands;

use App\Models\ConfigRappel;
use App\Models\Inscription;
use App\Models\Frais;
use App\Models\Paiement;
use App\Models\RappelPaiement;
use App\Mail\RappelPaiementMail;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RappelsPaiement extends Command
{
    protected $signature = 'rappels:envoyer';
    protected $description = 'Envoie les rappels de paiement automatiques aux élèves selon la configuration';

    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    public function handle(): int
    {
        $this->info('🔔 Début de l\'envoi des rappels de paiement...');
        $totalEnvoyes = 0;
        $totalErreurs = 0;

        // Récupérer toutes les configurations actives
        $configs = ConfigRappel::where('actif', true)->get();

        if ($configs->isEmpty()) {
            $this->warn('⚠️  Aucune configuration de rappel active trouvée.');
            return Command::SUCCESS;
        }

        foreach ($configs as $config) {
            $this->info("📋 École #{$config->ecole_id} - Fréquence: {$config->frequence}");

            // Vérifier si aujourd'hui on doit envoyer selon la fréquence
            if (!$this->doitEnvoyerAujourdhui($config)) {
                $this->line("   ⏭️  Pas d'envoi programmé pour aujourd'hui.");
                continue;
            }

            // Récupérer toutes les inscriptions actives de cette école
            $inscriptions = Inscription::where('ecole_id', $config->ecole_id)
                ->where('statut', 'actif')
                ->with(['eleve', 'classe', 'paiements', 'ecole'])
                ->get();

            foreach ($inscriptions as $inscription) {
                try {
                    $resultat = $this->traiterInscription($inscription, $config);

                    if ($resultat['envoye']) {
                        $totalEnvoyes++;
                        $this->line("   ✅ {$inscription->eleve->nom} {$inscription->eleve->postnom} - {$resultat['canal']}");
                    } else {
                        $this->line("   ℹ️  {$inscription->eleve->nom} {$inscription->eleve->postnom} - {$resultat['raison']}");
                    }

                } catch (\Exception $e) {
                    $totalErreurs++;
                    $this->error("   ❌ Erreur pour {$inscription->eleve->nom}: {$e->getMessage()}");
                    Log::error('[RappelsPaiement] Erreur traitement inscription', [
                        'inscription_id' => $inscription->id,
                        'erreur' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->newLine();
        $this->info("✅ Traitement terminé !");
        $this->line("   📨 Rappels envoyés: {$totalEnvoyes}");
        $this->line("   ❌ Erreurs: {$totalErreurs}");

        return Command::SUCCESS;
    }

    /**
     * Vérifier si la configuration nécessite un envoi aujourd'hui.
     */
    private function doitEnvoyerAujourdhui(ConfigRappel $config): bool
    {
        $now = now();

        switch ($config->frequence) {
            case 'hebdomadaire':
                // Vérifier le jour de la semaine (monday, tuesday, etc.)
                return strtolower($now->format('l')) === strtolower($config->jour_envoi);

            case 'mensuel':
                // Vérifier le jour du mois
                $jourMois = (int) $now->format('d');
                return $jourMois === (int) ($config->jour_du_mois ?? 1);

            case 'trimestriel':
                // Envoyer tous les 3 mois (début de trimestre)
                $mois = (int) $now->format('m');
                $jour = (int) $now->format('d');
                return in_array($mois, [1, 4, 7, 10]) && $jour === (int) ($config->jour_du_mois ?? 1);

            case 'semestriel':
                // Envoyer tous les 6 mois
                $mois = (int) $now->format('m');
                $jour = (int) $now->format('d');
                $jourCible = (int) ($config->jour_du_mois ?? 1);
                return ($mois === 1 || $mois === 7) && $jour === $jourCible;

            default:
                return false;
        }
    }

    /**
     * Traiter une inscription : calculer le solde et envoyer le rappel si nécessaire.
     */
    private function traiterInscription(Inscription $inscription, ConfigRappel $config): array
    {
        // Calculer le total dû pour cette inscription
        $fraisInscription = Frais::where('classe_id', $inscription->classe_id)
            ->where('annee_scolaire', $inscription->annee_scolaire)
            ->get();

        $montantDu = $fraisInscription->sum('montant');

        if ($montantDu <= 0) {
            return ['envoye' => false, 'raison' => 'Aucun frais configuré'];
        }

        $totalPaye = Paiement::where('inscription_id', $inscription->id)
            ->sum('montant_paye');

        $solde = $montantDu - $totalPaye;

        if ($solde <= 0) {
            return ['envoye' => false, 'raison' => 'Solde nul ou créditeur'];
        }

        // Déterminer la destination email
        $emailDestinataire = $inscription->eleve->email 
            ?? $inscription->email_parent 
            ?? $inscription->eleve->user->email 
            ?? null;

        $smsDestinataire = $inscription->eleve->telephone 
            ?? $inscription->telephone_parent 
            ?? null;

        if (!$config->email_actif && !$config->sms_actif) {
            return ['envoye' => false, 'raison' => 'Email et SMS désactivés dans la config'];
        }

        if (!$emailDestinataire && !$smsDestinataire) {
            return ['envoye' => false, 'raison' => 'Aucun contact disponible'];
        }

        // Créer l'enregistrement du rappel
        $rappel = RappelPaiement::create([
            'ecole_id' => $config->ecole_id,
            'inscription_id' => $inscription->id,
            'frais_id' => $fraisInscription->first()?->id,
            'montant_du' => $montantDu,
            'montant_paye' => $totalPaye,
            'solde' => $solde,
            'type_rappel' => $config->frequence,
            'statut' => 'en_attente',
            'email_destinataire' => $emailDestinataire,
            'sms_destinataire' => $smsDestinataire,
        ]);

        $canalEnvoye = [];
        $errors = [];

        // Envoyer l'email
        if ($config->email_actif && $emailDestinataire) {
            try {
                $frais = $fraisInscription->first();
                Mail::to($emailDestinataire)->send(
                    new RappelPaiementMail(
                        $inscription,
                        $frais,
                        $montantDu,
                        $totalPaye,
                        $solde
                    )
                );
                $rappel->email_envoye = true;
                $canalEnvoye[] = 'email';
                $this->line("      📧 Email envoyé à {$emailDestinataire}");
            } catch (\Exception $e) {
                $errors[] = "Email: {$e->getMessage()}";
                Log::error('[RappelsPaiement] Erreur envoi email', [
                    'destinataire' => $emailDestinataire,
                    'erreur' => $e->getMessage(),
                ]);
            }
        }

        // Envoyer le SMS
        if ($config->sms_actif && $smsDestinataire) {
            try {
                $messageSms = $this->genererMessageSms($inscription, $solde, $config);
                $smsOk = $this->smsService->envoyer($smsDestinataire, $messageSms);

                if ($smsOk) {
                    $rappel->sms_envoye = true;
                    $canalEnvoye[] = 'SMS';
                    $this->line("      💬 SMS envoyé à {$smsDestinataire}");
                } else {
                    $errors[] = 'Échec envoi SMS (voir logs)';
                }
            } catch (\Exception $e) {
                $errors[] = "SMS: {$e->getMessage()}";
                Log::error('[RappelsPaiement] Erreur envoi SMS', [
                    'destinataire' => $smsDestinataire,
                    'erreur' => $e->getMessage(),
                ]);
            }
        }

        // Mettre à jour le statut du rappel
        if (!empty($canalEnvoye)) {
            $rappel->statut = 'envoye';
            $rappel->date_envoi = now();
            $rappel->message_erreur = !empty($errors) ? implode('; ', $errors) : null;
            $rappel->save();

            return [
                'envoye' => true,
                'canal' => 'Envoyé par ' . implode(' + ', $canalEnvoye),
            ];
        } else {
            $rappel->statut = 'echoue';
            $rappel->message_erreur = !empty($errors) ? implode('; ', $errors) : 'Aucun canal disponible';
            $rappel->save();

            return [
                'envoye' => false,
                'raison' => 'Échec: ' . ($rappel->message_erreur),
            ];
        }
    }

    /**
     * Générer le message SMS pour un rappel.
     */
    private function genererMessageSms(Inscription $inscription, float $solde, ConfigRappel $config): string
    {
        $messageParDefaut = "RAPPEL: Cher parent, {$inscription->eleve->nom} {$inscription->eleve->postnom} (Classe: {$inscription->classe->nom_classe}) a un solde impayé de {$solde} USD. Merci de régulariser au plus tôt. {$inscription->ecole->nom_ecole}";

        if (!empty($config->message_personnalise)) {
            // Remplacer les variables dans le message personnalisé
            $message = str_replace(
                ['{eleve}', '{classe}', '{solde}', '{ecole}', '{annee}'],
                [
                    "{$inscription->eleve->nom} {$inscription->eleve->postnom}",
                    $inscription->classe->nom_classe ?? '—',
                    number_format($solde, 2),
                    $inscription->ecole->nom_ecole ?? 'Établissement',
                    $inscription->annee_scolaire,
                ],
                $config->message_personnalise
            );
            return $message;
        }

        return $messageParDefaut;
    }
}

