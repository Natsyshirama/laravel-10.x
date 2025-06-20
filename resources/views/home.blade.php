<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Moderne - @yield('title')</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Ajoutez Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="app-sidebar">
            <div class="sidebar-logo">
                <h2>ERP PRO</h2>
            </div>
            <nav class="sidebar-nav">
    <!-- <a href="{{ route('dashboard.achats') }}" class="nav-item">
        <i class="fas fa-chart-line"></i> Dashboard
    </a> -->
    <!-- <a href="{{ route('devis.filtre') }}" class="nav-item">
        <i class="fas fa-file-signature"></i> Devis
    </a>
    <a href="{{ route('commandes.filtre') }}" class="nav-item">
        <i class="fas fa-shopping-cart"></i> Commandes
    </a>
    <a href="{{ route('factures.achat.index') }}" class="nav-item">
        <i class="fas fa-file-invoice-dollar"></i> Mode comptabilité
    </a>
    <a href="{{ route('profile') }}" class="nav-item">
        <i class="fas fa-user-circle"></i> Mon Profil
    </a>
    <a href="{{ route('client.index') }}" class="nav-item">
        <i class="fas fa-users"></i> Clients
    </a>
    <a href="{{ route('devisClient.index') }}" class="nav-item">
        <i class="fas fa-file-alt"></i> Devis Client
    </a>
    <a href="{{ route('comdClient.index') }}" class="nav-item">
        <i class="fas fa-box-open"></i> Commandes Client
    </a>
    <a href="{{ route('livraison.index') }}" class="nav-item">
        <i class="fas fa-truck-loading"></i> Bon de livraison
    </a> -->
    <a href="{{ route('employee.index') }}" class="nav-item">
        <i class="fas fa-user-tie"></i> Employees
    </a>
    <a href="{{ route('fiche-paie.liste') }}" class="nav-item">
        <i class="fas fa-file"></i> Fiche Paie
    </a>
    <a href="{{ route('import.index') }}" class="nav-item">
        <i class="fas fa-file-import"></i> Import Donne
    </a>
    <a href="{{ route('fichePaie.filtreParMois') }}" class="nav-item">
        <i class="fas fa-table"></i> Tableau Salary Slip Month
    </a>

    <a href="{{ route('fichePaie.resumeParAnnee') }}" class="nav-item">
        <i class="fas fa-table"></i> Salary Slip
    </a>
    <a href="{{ route('salaraStr.index') }}" class="nav-item">
        <i class="fas fa-layer-group"></i> Salary Structur
    </a>
    <a href="{{ route('fichePaie.grapheSalaire') }}" class="nav-item">
        <i class="fas fa-chart-line"></i> Dashboard
    </a>

    <a href="{{route('salary.modifForm')}}" class="nav-item">
        <i class="fas fa-"></i> modif salaire
    </a>
    
    
    <a href="{{ route('genere.genForm') }}" class="nav-item">
        <i class="fas fa-add"></i> generer salaire
    </a>
</nav>

        </aside>

        <!-- Header -->
        <header class="app-header">
            
        <div class="header-actions ml-auto">
    <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger">
        <i class="fas fa-sign-out-alt"></i> Déconnexion
    </a>
</div>
        </header>

        <!-- Main Content -->
        <main class="app-content">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

 <!-- Charger jQuery en premier -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Puis Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Scripts spécifiques à la vue -->
    @yield('scripts')</body>
</html>