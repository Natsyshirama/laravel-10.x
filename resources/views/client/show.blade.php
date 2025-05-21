@extends('home')

@section('title', 'Détails du Client')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">Détails du client : {{ $client['name'] }}</h2>
            <div class="btn-group">
            <div class="btn-group">
                @if($client['disabled'] == 0)
                    <form action="{{ route('client.desactiver', $client['name']) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-ban"></i> Désactiver
                        </button>
                    </form>
                @else
                    <form action="{{ route('client.activer', $client['name']) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Activer
                        </button>
                    </form>
                @endif
                <button class="btn btn-secondary">
                    <i class="fas fa-print"></i> Imprimer
                </button>
                <a href="{{ route('client.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle"></i> Erreur :</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="info-group">
                    <h5>Informations générales</h5>
                    <div class="info-item">
                        <span class="info-label">Name :</span>
                        <span class="info-value">{{ $client['customer_name'] }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Type :</span>
                        <span class="info-value">{{ $client['customer_type'] ?? 'pas de type'}}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Territory :</span>
                        <span class="info-value">{{ $client['territory'] ?? 'pas de region' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">creation :</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($client['creation'])->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Statut :</span>
                        <span class="info-value badge 
                            @if($client['disabled'] == 0) bg-success
                            @else bg-warning
                            @endif">
                            {{ $client['disabled'] == 0 ? 'Activer' : 'Desactiver' }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Owner :</span>
                        <span class="info-value">{{ $client['owner'] }}</span>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .info-group {
        background: #f8fafc;
        border-radius: 8px;
        padding: 1rem;
    }
    
    .info-item {
        display: flex;
        margin-bottom: 0.5rem;
    }
    
    .info-label {
        font-weight: 600;
        width: 150px;
        color: #64748b;
    }
    
    .info-value {
        flex: 1;
    }
    
    .items-table input {
        min-width: 80px;
    }
    
    .select2-container {
        width: 100% !important;
    }
    .alert alert-danger{
        color:rgb(255, 0, 0);

    }.alert alert-success{
        color:rgb(0, 254, 97);

    }
</style>
@endpush
@endsection