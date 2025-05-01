<!DOCTYPE html>
<html>
<head>
    <title>Profil Utilisateur</title>
</head>
<body>
    <h2>Profil Utilisateur</h2>

    <p><strong>Nom :</strong> {{ $user['first_name'] }} {{ $user['last_name'] }}</p>
    <p><strong>Email :</strong> {{ $user['email'] }}</p>
    <p><strongGenre :</strong> {{ $user['gender'] ?? 'Non spécifié' }}</p>
    <p><strong>Date de création :</strong> {{ $user['creation'] }}</p>

    <a href="{{ route('logout') }}">Déconnexion</a>
</body>
</html>
