@extends('home')

@section('title', 'Dashboard Achats')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-chart-line"></i> Tableau de bord des achats
        </h2>
    </div>

    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first('message') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session()->get('error') }}
            </div>
        @endif

        <div class="row stats-row">
            <!-- Carte Devis -->
            <div class="col-md-6">
                <div class="stat-card bg-primary-light">
                    <div class="stat-icon">
                        <i class="fas fa-file-invoice text-primary"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Devis fournisseurs</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <span class="label">Nombre total :</span>
                                <span class="value">{{ $stats['devis_count'] }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Total montants :</span>
                                <span class="value">{{ number_format($stats['devis_total'], 2) }} €</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte Commandes -->
            <div class="col-md-6">
                <div class="stat-card bg-warning-light">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart text-warning"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Commandes d'achat</h3>
                        <div class="stat-values">
                            <div class="stat-item">
                                <span class="label">Nombre total :</span>
                                <span class="value">{{ $stats['commande_count'] }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Total montants :</span>
                                <span class="value">{{ number_format($stats['commande_total'], 2) }} €</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique supplémentaire -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Activité récente</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="purchaseActivityChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique d'activité
    const ctx = document.getElementById('purchaseActivityChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            datasets: [
                {
                    label: 'Devis',
                    data: [12, 19, 15, 20, 18, 22],
                    backgroundColor: '#4361ee',
                    borderRadius: 4
                },
                {
                    label: 'Commandes',
                    data: [8, 12, 10, 15, 14, 18],
                    backgroundColor: '#f8961e',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush

<style>
    .stat-card {
        display: flex;
        padding: 1.5rem;
        border-radius: 12px;
        height: 100%;
    }
    
    .stat-icon {
        font-size: 2.5rem;
        margin-right: 1.5rem;
        display: flex;
        align-items: center;
    }
    
    .stat-content h3 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: var(--light);
    }
    
    .stat-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    
    .stat-item .label {
        color: var(--gray);
    }
    
    .stat-item .value {
        font-weight: 600;
    }
    
    .bg-primary-light {
        background-color: rgba(0, 53, 244, 0.39);
    }
    
    .bg-warning-light {
        background-color: rgba(248, 150, 30, 0.1);
    }
</style>
@endsection