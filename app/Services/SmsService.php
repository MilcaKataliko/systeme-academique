<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Envoyer un SMS.
     * 
     * @param string $destinataire Numéro de téléphone du destinataire
     * @param string $message Contenu du message
     * @return bool Succès ou échec
     */
    public function envoyer(string $destinataire, string $message): bool
    {
        // Nettoyer le numéro
        $telephone = $this->formaterNumero($destinataire);
        
        if (!$telephone) {
            Log::warning('[SmsService] Numéro de téléphone invalide', [
                'original' => $destinataire
            ]);
            return false;
        }

        try {
            // === IMPLÉMENTATION RÉELLE ===
            // Décommentez et configurez selon votre fournisseur SMS :
            
            // Option 1: Twilio
            // $twilio = new \Twilio\Rest\Client(
            //     env('TWILIO_SID'), 
            //     env('TWILIO_TOKEN')
            // );
            // $twilio->messages->create($telephone, [
            //     'from' => env('TWILIO_FROM'),
            //     'body' => $message
            // ]);
            
            // Option 2: Vonage (Nexmo)
            // $basic = new \Vonage\Client\Credentials\Basic(
            //     env('VONAGE_KEY'), 
            //     env('VONAGE_SECRET')
            // );
            // $client = new \Vonage\Client($basic);
            // $client->sms()->send(
            //     new \Vonage\SMS\Message\SMS($telephone, env('VONAGE_FROM'), $message)
            // );
            
            // Option 3: Africa's Talking
            // $at = new \AfricasTalking\SDK\AfricasTalking(
            //     env('AT_USERNAME'), 
            //     env('AT_API_KEY')
            // );
            // $at->sms()->send([
            //     'to' => $telephone,
            //     'message' => $message,
            //     'from' => env('AT_FROM', 'SCOLAIRE')
            // ]);

            // Pour l'instant : Logguer le SMS (mode développement/test)
            Log::info('[SmsService] SMS envoyé avec succès (simulation)', [
                'destinataire' => $telephone,
                'message' => $message,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('[SmsService] Erreur envoi SMS', [
                'destinataire' => $telephone,
                'erreur' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Formater le numéro de téléphone au format international.
     */
    private function formaterNumero(?string $numero): ?string
    {
        if (empty($numero)) {
            return null;
        }

        // Nettoyer le numéro
        $numero = preg_replace('/[^0-9+]/', '', $numero);

        // Si le numéro commence par 0 (RDC: 081, 082, 097, etc.)
        if (str_starts_with($numero, '0')) {
            $numero = '+243' . substr($numero, 1);
        }
        // Si le numéro commence par 243 sans +
        elseif (str_starts_with($numero, '243')) {
            $numero = '+' . $numero;
        }
        // Si le numéro n'a pas d'indicatif
        elseif (!str_starts_with($numero, '+')) {
            $numero = '+243' . $numero;
        }

        // Validation basique : doit faire au moins 12 caractères (+243XXX...)
        if (strlen($numero) < 12) {
            return null;
        }

        return $numero;
    }
}

