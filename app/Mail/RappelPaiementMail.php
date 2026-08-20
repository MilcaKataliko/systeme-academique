<?php

namespace App\Mail;

use App\Models\Inscription;
use App\Models\Frais;
use App\Models\Ecole;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RappelPaiementMail extends Mailable
{
    use Queueable, SerializesModels;

    public Inscription $inscription;
    public Frais $frais;
    public float $montantDu;
    public float $montantPaye;
    public float $solde;
    public Ecole $ecole;

    public function __construct(
        Inscription $inscription,
        Frais $frais,
        float $montantDu,
        float $montantPaye,
        float $solde
    ) {
        $this->inscription = $inscription;
        $this->frais = $frais;
        $this->montantDu = $montantDu;
        $this->montantPaye = $montantPaye;
        $this->solde = $solde;
        $this->ecole = $inscription->ecole;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Rappel de Paiement - {$this->ecole->nom_ecole}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rappels.paiement',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

