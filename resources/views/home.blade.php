<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <a href="{{ route('profile') }}">Mon Profil</a>
    <h2>Bienvenue, {{ $userName }}</h2>
    <a href="{{ route('devis.index') }}">Liste des Devis Fournisseurs</a>
    <p>Ceci est votre tableau de bord.</p>

    <a href="{{ route('logout') }}">Se déconnecter</a>
</body>
</html>
