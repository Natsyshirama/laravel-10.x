@extends('home')

@section('title', 'Liste des Devis')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">Liste des Devis</h2>
            <a href="{{ route('devis.filtre') }}" class="btn btn-outline-primary">
                <i class="fas fa-filter"></i> Filtrer par fournisseur
            </a>
        </div>
    </div>

    <div class="card-body">
        @if (count($devis) > 0)
            <div class="table-responsive">
                <table class="data-table table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Fournisseur</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Valable jusqu'à</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($devis as $devi)
                            <tr>
                                <td>
                                    <a href="{{ route('devis.show', ['name' => $devi['name']]) }}" class="text-primary">
                                        {{ $devi['name'] }}
                                    </a>
                                </td>
                                <td>{{ $devi['supplier_name'] ?? $devi['supplier'] }}</td>
                                <td>
                                    <span class="badge 
                                        @if($devi['status'] == 'Validé') bg-success
                                        @elseif($devi['status'] == 'En attente') bg-warning
                                        @else bg-secondary
                                        @endif">
                                        {{ $devi['status'] }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($devi['transaction_date'])->format('d/m/Y') }}</td>
                                <td class="font-weight-bold">{{ number_format($devi['grand_total'], 2) }} {{ $devi['currency'] }}</td>
                                <td>{{ $devi['valid_till'] ? \Carbon\Carbon::parse($devi['valid_till'])->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('devis.show', ['name' => $devi['name']]) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucun devis trouvé
            </div>
        @endif
    </div>
</div>
@endsection