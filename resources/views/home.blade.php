<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
@if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <a href="{{ route('profile') }}">Mon Profil</a>
    <h2>Bienvenue, {{ $userName }}</h2>
    <a href="{{ route('devis.filtre') }}"> Devis Fournisseurs</a></br>
   <a href="{{ route('commandes.filtre') }}">Commandes d'Achat</a>
   <a href="{{ route('factures.achat.index') }}">Factures d'Achat</a> 
   <p>Ceci est votre tableau de bord.</p>

    <a href="{{ route('logout') }}">Se déconnecter</a>
</body>
</html>
