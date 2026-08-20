<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappel de Paiement</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e3a5f, #2d5a87);
            padding: 30px 40px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 800;
        }
        .header p {
            color: #a8c8e8;
            margin: 8px 0 0;
            font-size: 14px;
        }
        .content {
            padding: 30px 40px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1e3a5f;
            margin-bottom: 20px;
        }
        .info-box {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e8ecf4;
        }
        .info-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-box td {
            padding: 8px 0;
            font-size: 14px;
            color: #4a5568;
        }
        .info-box td:last-child {
            text-align: right;
            font-weight: 600;
            color: #1e3a5f;
        }
        .amount-due {
            font-size: 22px;
            font-weight: 800;
            color: #e53e3e;
            text-align: center;
            padding: 15px;
            background: #fff5f5;
            border-radius: 12px;
            border: 1px solid #fed7d7;
            margin-bottom: 20px;
        }
        .cta-button {
            display: block;
            width: 200px;
            margin: 25px auto;
            padding: 14px 20px;
            background: #2d5a87;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 12px;
            text-align: center;
            font-weight: 700;
            font-size: 16px;
        }
        .cta-button:hover {
            background: #1e3a5f;
        }
        .footer {
            padding: 20px 40px;
            background: #f8f9fc;
            text-align: center;
            color: #718096;
            font-size: 12px;
            border-top: 1px solid #e8ecf4;
        }
        .footer p {
            margin: 4px 0;
        }
        .footer a {
            color: #2d5a87;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📌 Rappel de Paiement</h1>
            <p>{{ $ecole->nom_ecole ?? 'Établissement scolaire' }}</p>
        </div>

        <div class="content">
            <div class="greeting">
                Bonjour {{ $inscription->eleve->nom }} {{ $inscription->eleve->postnom }},
            </div>

            <p style="color: #4a5568; font-size: 15px; line-height: 1.6;">
                Nous vous rappelons que vous avez des frais de scolarité en attente de paiement pour 
                l'année académique <strong>{{ $inscription->annee_scolaire }}</strong>.
                Nous vous prions de bien vouloir régulariser votre situation dans les meilleurs délais.
            </p>

            <div class="info-box">
                <table>
                    <tr>
                        <td>Classe</td>
                        <td>{{ $inscription->classe->nom_classe ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Année scolaire</td>
                        <td>{{ $inscription->annee_scolaire }}</td>
                    </tr>
                    <tr>
                        <td>Frais concerné</td>
                        <td>{{ $frais->intitule_frais ?? 'Frais de scolarité' }}</td>
                    </tr>
                    <tr>
                        <td>Montant total dû</td>
                        <td>{{ number_format($montantDu, 2) }} $</td>
                    </tr>
                    <tr>
                        <td>Montant déjà payé</td>
                        <td style="color: #38a169;">{{ number_format($montantPaye, 2) }} $</td>
                    </tr>
                </table>
            </div>

            <div class="amount-due">
                Solde impayé : {{ number_format($solde, 2) }} $
            </div>

            <p style="color: #4a5568; font-size: 14px; line-height: 1.6; text-align: center;">
                Merci de procéder au paiement auprès du service comptable de votre établissement.
                Pour toute question, veuillez contacter l'administration.
            </p>

            <a href="#" class="cta-button">Contacter le comptable</a>
        </div>

        <div class="footer">
            <p><strong>{{ $ecole->nom_ecole ?? 'Système Académique' }}</strong></p>
            <p>{{ $ecole->adresse ?? '' }}</p>
            <p style="margin-top: 8px;">
                Ce message est automatisé. Merci de ne pas y répondre.
            </p>
            <p style="margin-top: 8px;">
                <a href="#">Se désabonner des rappels</a>
            </p>
        </div>
    </div>
</body>
</html>

