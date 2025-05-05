@extends('home')

@section('title', 'Détails Facture')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">
                <i class="fas fa-file-invoice"></i> Facture : {{ $facture['name'] }}
            </h2>
            <div class="btn-group">
                <button class="btn btn-secondary">
                    <i class="fas fa-print"></i> export
                </button>
                @if($facture['status'] != 'Payé')
                <button class="btn btn-success">
                    <i class="fas fa-check"></i> Marquer comme payé
                </button>
                @endif
                <a href="{{ route('factures.achat.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="info-card-title">Fournisseur</h5>
                    <div class="info-card-content">
                        <div class="info-item">
                            <span class="info-label">Nom :</span>
                            <span class="info-value">{{ $facture['supplier'] ?? $facture['supplier_name'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Entreprise :</span>
                            <span class="info-value">{{ $facture['company'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="info-card-title">Informations</h5>
                    <div class="info-card-content">
                        <div class="info-item">
                            <span class="info-label">Statut :</span>
                            <span class="info-value badge 
                                @if($facture['status'] == 'Payé') bg-success
                                @elseif($facture['status'] == 'En retard') bg-danger
                                @else bg-warning
                                @endif">
                                {{ $facture['status'] }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date :</span>
                            <span class="info-value">
                                {{ \Carbon\Carbon::parse($facture['posting_date'])->format('d/m/Y') }}
                                à {{ $facture['posting_time'] }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Total :</span>
                            <span class="info-value font-weight-bold text-primary">
                                {{ number_format($facture['grand_total'], 2) }} {{ $facture['currency'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!empty($facture['items']))
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-boxes"></i> Articles facturés ({{ count($facture['items']) }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-items">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="35%">Article</th>
                                <th width="15%">Quantité</th>
                                <th width="10%">Unité</th>
                                <th width="15%">Prix unitaire</th>
                                <th width="15%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($facture['items'] as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $item['item_name'] }}</div>
                                    <div class="text-muted small">{{ $item['item_code'] }}</div>
                                </td>
                                <td>{{ $item['qty'] }}</td>
                                <td>{{ $item['uom'] }}</td>
                                <td class="text-right">{{ number_format($item['rate'], 2) }} {{ $facture['currency'] }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($item['amount'], 2) }} {{ $facture['currency'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                                <td class="text-right font-weight-bold">Total :</td>
                                <td class="text-right font-weight-bold text-primary">{{ number_format($facture['grand_total'], 2) }} {{ $facture['currency'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .info-card {
        background:rgb(255, 255, 255);
        border-radius: 8px;
        overflow: hidden;
        height: 100%;
    }
    
    .info-card-title {
        background:rgb(239, 237, 237);
        padding: 0.75rem 1rem;
        font-size: 1rem;
        font-weight: 600;
    }
    
    .info-card-content {
        padding: 1rem;
    }
    
    .info-item {
        display: flex;
        margin-bottom: 0.75rem;
    }
    
    .info-label {
        font-weight: 500;
        color:rgb(0, 0, 0);
        min-width: 100px;
    }
    
    .info-value {
        flex: 1;
    }
    
    .table-items td, .table-items th {
        vertical-align: middle;
    }
</style>
@endsection