<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Année Scolaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Nouvelle Année Scolaire</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('annees.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="anneescolaire" class="form-label">Libellé de l'année (ex: 2025-2026)</label>
                        <input type="text" name="anneescolaire" id="anneescolaire" class="form-control" required placeholder="Ex: 2025-2026">
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('annees.index') }}" class="btn btn-secondary">Retour</a>
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>