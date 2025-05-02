<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
</head>
<body>
    <h2>Connexion</h2>

    @if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div style="color:green;">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <label>Nom d'utilisateur:</label><br>
        <input type="text" name="usr" value="{{ old('usr') }}"><br><br>

        <label>Mot de passe:</label><br>
        <input type="password" name="pwd"><br><br>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
