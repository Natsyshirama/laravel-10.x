<!DOCTYPE html>
<html>
<head>
    <title>Login ERPNext</title>
</head>
<body>
    <h2>Connexion</h2>
    @if($errors->any())
        <p style="color:red;">{{ $errors->first() }}</p>
    @endif
    <form method="POST" action="{{ route('do.login') }}">
        @csrf
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Mot de passe:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
