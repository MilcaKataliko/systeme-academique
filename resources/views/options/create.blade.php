<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Option</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Nouvelle Option</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('options.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nomoption" class="form-label">Nom complet de l'option</label>
                        <input type="text" name="nomoption" id="nomoption" class="form-control" required placeholder="Ex: Sciences Informatiques">
                    </div>
                    <div class="mb-3">
                        <label for="sigle" class="form-label">Sigle / Abréviation</label>
                        <input type="text" name="sigle" id="sigle" class="form-control" required placeholder="Ex: Sc. Info">
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('options.index') }}" class="btn btn-secondary">Retour</a>
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>