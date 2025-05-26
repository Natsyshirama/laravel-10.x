@extends('home')

@section('title', 'Détails Devis Client')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">
                <i class="fas fa-file-invoice"></i> Devis Client : {{ $quotation['name'] }}
            </h2>
            <div class="btn-group">
    <!-- Bouton Export PDF -->
    @if($quotation['status'] != 'Ordered')
    @if($quotation['status'] == 'Draft')
            <form method="POST" action="{{ route('devisClient.validate', $quotation['name']) }}">
    @csrf
                <button type="submit" class="btn btn-warning" title="Valider le Devis">
                        <i class="fas fa-check-circle"></i> Valider
                </button>
            </form>
            @endif
            <form action="{{ route('devisClient.commander', $quotation['name']) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check"></i> Commander
            </button>
        </form>
        @endif
            <a href="{{ route('devisClient.index') }}" class="btn btn-light">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>
        </div>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="info-card-title">Client</h5>
                    <div class="info-card-content">
                        <div class="info-item">
                            <span class="info-label">Nom :</span>
                            <span class="info-value">{{  $quotation['customer_name'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Entreprise :</span>
                            <span class="info-value">{{ $quotation['company'] }}</span>
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
                                @if($quotation['status'] == 'ordered') bg-success
                                @elseif($quotation['status'] == 'draft') bg-danger
                                @else bg-warning
                                @endif">
                                {{ $quotation['status'] }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date :</span>
                            <span class="info-value">
                                {{ \Carbon\Carbon::parse($quotation['transaction_date'])->format('d/m/Y') }}
                               
                            </span>
                            <span class="info-label">Date Vallid:</span>
                            <span class="info-value">
                                {{ \Carbon\Carbon::parse($quotation['valid_till'])->format('d/m/Y') }}
                               
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Total :</span>
                            <span class="info-value font-weight-bold text-primary">
                                {{ number_format($quotation['base_total'], 2) }} {{ $quotation['currency'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!empty($quotation['items']))
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-boxes"></i> Articles deviseer ({{ count($quotation['items']) }})
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
                            @foreach ($quotation['items'] as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $item['item_name'] }}</div>
                                    <div class="text-muted small">{{ $item['item_code'] }}</div>
                                </td>
                                <td>{{ $item['qty'] }}</td>
                                <td>{{ $item['uom'] }}</td>
                                <td class="text-right">{{ number_format($item['rate'], 2) }} {{ $quotation['currency'] }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($item['amount'], 2) }} {{ $quotation['currency'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                                <td class="text-right font-weight-bold">Total :</td>
                                <td class="text-right font-weight-bold text-primary">{{ number_format($quotation['grand_total'], 2) }} {{ $quotation['currency'] }}</td>
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
    .info-section { display: flex; justify-content: space-between; margin-bottom: 20px; }
        
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background-color: #f2f2f2; text-align: left; padding: 5px; }
    td { padding: 5px; border-bottom: 1px solid #ddd; }
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