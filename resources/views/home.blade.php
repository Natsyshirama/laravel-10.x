<!DOCTYPE html>
<html>
<head>
    <title>Accueil</title>
</head>
<body>
@if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

    <h1>Bienvenue, {{ $user }}</h1>
    <a href="{{ route('profile.show') }}">Voir mon profil</a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Se déconnecter</button>
    </form>
</body>
</html>
