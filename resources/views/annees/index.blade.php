<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Années Scolaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestion des Années Scolaires</h2>
            <a href="{{ route('annees.create') }}" class="btn btn-primary">Ajouter une Année</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Année Scolaire</th>
                            <th>Date de création</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($annees as $annee)
                            <tr>
                                <td>{{ $annee->idAnnee }}</td>
                                <td>{{ $annee->anneescolaire }}</td>
                                <td>{{ $annee->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>