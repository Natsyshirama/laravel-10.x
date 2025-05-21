@extends('home')

@section('title', 'Commandes d\'Achat')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">
                <i class="fas fa-shopping-cart"></i> Liste des Commandes d'Achat
            </h2>
            <div>
                <a href="{{ route('commandes.filtre') }}" class="btn btn-outline-primary">
                    <i class="fas fa-filter"></i> Filtrer
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('commandes.index') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="type" class="form-label">Statut de commande</label>
                    <select name="type" id="type" class="form-control select2-status">
                        <option value="">-- Tous les statuts --</option>
                        <option value="paye" {{ $selectedType == 'paye' ? 'selected' : '' }}>Entièrement facturé</option>
                        <option value="non_paye" {{ $selectedType == 'non_paye' ? 'selected' : '' }}>Non facturé</option>
                        <option value="non_recu" {{ $selectedType == 'non_recu' ? 'selected' : '' }}>Non reçu</option>
                        <option value="recu" {{ $selectedType == 'recu' ? 'selected' : '' }}>Entièrement reçu</option>
                        <option value="paye_recu" {{ $selectedType == 'paye_recu' ? 'selected' : '' }}>Partiellement payé et reçu</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Appliquer
                    </button>
                </div>
            </div>
        </form>

        @if (count($commandes) > 0)
            <div class="table-responsive">
                <table class="data-table table-hover">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Fournisseur</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($commandes as $commande)
                            <tr>
                                <td>
                                    <a href="{{ route('commandes.show', ['name' => $commande['name']]) }}" class="text-primary font-weight-bold">
                                        {{ $commande['name'] }}
                                    </a>
                                </td>
                                <td>{{ $commande['supplier'] ?? $commande['supplier_name'] }}</td>
                                <td>
                                    <span class="badge 
                                        @if(str_contains($commande['status'], 'Reçu')) bg-success
                                        @elseif(str_contains($commande['status'], 'Facturé')) bg-info
                                        @elseif(str_contains($commande['status'], 'Annulé')) bg-danger
                                        @else bg-warning
                                        @endif">
                                        {{ $commande['status'] }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($commande['transaction_date'])->format('d/m/Y') }}</td>
                                <td class="font-weight-bold">{{ number_format($commande['grand_total'], 2) }} {{ $commande['currency'] }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('commandes.show', ['name' => $commande['name']]) }}" class="btn btn-info" title="Voir">
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
                <i class="fas fa-info-circle"></i> Aucune commande d'achat trouvée
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-status').select2({
            minimumResultsForSearch: Infinity
        });
    });
</script>
@endpush