<!DOCTYPE html>
<html>
<head>
    <title>Utilisateur Connecté</title>
</head>
<body>
    <h2>Informations de session</h2>

    @if (isset($error))
        <p style="color:red;">Erreur : {{ $error }}</p>
    @endif

    @if ($sid)
        <p><strong>SID :</strong> {{ $sid }}</p>
    @else
        <p style="color:red;">Aucun SID récupéré.</p>
    @endif

    @if ($user)
        <h3>Utilisateur connecté :</h3>
        <pre>{{ print_r($user, true) }}</pre>
    @else
        <p style="color:red;">Aucun utilisateur récupéré.</p>
    @endif

    <a href="{{ route('dashboard') }}">Retour au dashboard</a>
</body>
</html>
