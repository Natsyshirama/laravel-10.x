<!DOCTYPE html>
<html>
<head>
    <title>Profil utilisateur</title>
</head>
<body>
    <h2>Profil de l'utilisateur</h2>

    <p><strong>Nom :</strong> {{ $profile['first_name'] ?? '' }} {{ $profile['last_name'] ?? '' }}</p>
    <p><strong>Email :</strong> {{ $profile['email'] }}</p>
    <p><strong>Genre :</strong> {{ $profile['gender'] ?? 'Non spécifié' }}</p>
    <p><strong>Date de création :</strong> {{ $profile['creation'] }}</p>

    <a href="{{ route('logout') }}">Se déconnecter</a>
</body>
</html>
