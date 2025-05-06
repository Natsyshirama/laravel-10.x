<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="{{ asset('css/loginstyl.css') }}">
</head>
<body>
    <div class="login-container">
        <h2>Connexion</h2>

        @if ($errors->any())
            <div class="error-message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <label>Nom d'utilisateur:</label>
            <input type="text" name="usr" value="{{ old('usr') }}">

            <label>Mot de passe:</label>
            <input type="password" name="pwd">

            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
