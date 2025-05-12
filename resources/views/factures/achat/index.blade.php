@extends('home')

@section('title', 'Factures d\'Achat')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> Factures d'Achat
            </h2>
            
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('factures.achat.index') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="type" class="form-label">Statut de paiement</label>
                    <select name="type" id="type" class="form-control select2-status">
                        <option value="">-- Tous les statuts --</option>
                        <option value="payer" {{ $selectType == 'payer' ? 'selected' : '' }}>Payé</option>
                        <option value="non_payer" {{ $selectType == 'non_payer' ? 'selected' : '' }}>Non Payé</option>
                        <option value="enretard" {{ $selectType == 'enretard' ? 'selected' : '' }}>En retard</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>

        @if(count($factures) > 0)
            <div class="table-responsive">
                <table class="data-table table-hover">
                    <thead>
                        <tr>
                            <th>N° Facture</th>
                            <th>Fournisseur</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($factures as $facture)
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="{{ route('factures.achat.show', ['name' => $facture['name']]) }}" class="text-primary">
                                        {{ $facture['name'] }}
                                    </a>
                                </td>
                                <td>{{ $facture['supplier'] }}</td>
                                <td>
                                    <span class="badge 
                                        @if($facture['status'] == 'Payé') bg-success
                                        @elseif($facture['status'] == 'En retard') bg-danger
                                        @else bg-warning
                                        @endif">
                                        {{ $facture['status'] }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($facture['posting_date'])->format('d/m/Y') }}</td>
                                <td class="font-weight-bold">{{ number_format($facture['grand_total'], 2) }} {{ $facture['currency'] }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('factures.achat.show', ['name' => $facture['name']]) }}" class="btn btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                       
                                    @if($facture['status'] != 'Paid' && $facture['status'] != 'Cancelled')
                                        @if($facture['status'] == 'Draft')
                                            <form method="POST" action="{{ route('factures.achat.validate', $facture['name']) }}">
                                            @csrf
                                                <button type="submit" class="btn btn-warning" title="Valider la facture">
                                                            <i class="fas fa-check-circle"></i> Valider
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('factures.achat.pay', $facture['name']) }}">
                                            @csrf
                                                <button class="btn btn-success" title="Marquer comme payé">
                                                    <i class="fas fa-money-check-alt"></i> Payer
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucune facture trouvée
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-status').select2({
            minimumResultsForSearch: Infinity
        });
    });
</script>
@endpush
@endsection