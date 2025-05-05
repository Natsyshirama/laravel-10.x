<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Moderne - @yield('title')</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="app-sidebar">
            <div class="sidebar-logo">
                <h2>ERP PRO</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard.achats') }}" class="nav-item">
                    <i class="fas fa-dashboard"></i> Dashboard
                </a>
                <a href="{{ route('devis.filtre') }}" class="nav-item">
                    <i class="fas fa-file-contract"></i> Devis
                </a>
                <a href="{{ route('commandes.filtre') }}" class="nav-item">
                    <i class="fas fa-truck"></i> Commandes
                </a>
                <a href="{{ route('factures.achat.index') }}" class="nav-item">
                    <i class="fas fa-file-invoice"></i> Mode comptabilite
                </a>
                <a href="{{ route('profile') }}" class="nav-item">
                    <i class="fas fa-user"></i> Mon Profil
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>