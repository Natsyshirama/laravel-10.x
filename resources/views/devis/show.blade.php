@extends('home')

@section('title', 'Détails du Devis')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">Détails du devis : {{ $devis['name'] }}</h2>
            <div class="btn-group">
                <button class="btn btn-secondary">
                    <i class="fas fa-print"></i> Imprimer
                </button>
                <a href="{{ route('devis.index') }}" class="btn btn-light">
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
                        <span class="info-label">Fournisseur :</span>
                        <span class="info-value">{{ $devis['supplier_name'] }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date :</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($devis['transaction_date'])->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Statut :</span>
                        <span class="info-value badge 
                            @if($devis['docstatus'] == 1) bg-success
                            @else bg-warning
                            @endif">
                            {{ $devis['docstatus'] == 1 ? 'Soumis' : 'Brouillon' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-group">
                    <h5>Validité & Montant</h5>
                    <div class="info-item">
                        <span class="info-label">Valable jusqu'au :</span>
                        <span class="info-value">{{ $devis['valid_till'] ? \Carbon\Carbon::parse($devis['valid_till'])->format('d/m/Y') : 'Non spécifié' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Montant total :</span>
                        <span class="info-value font-weight-bold">{{ number_format($devis['grand_total'], 2) }} {{ $devis['currency'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('devis.update-and-submit', ['name' => $devis['name']]) }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-bordered items-table">
                    <thead class="bg-light">
                        <tr>
                            <th width="40%">Article</th>
                            <th width="15%">Quantité</th>
                            <th width="15%">Prix Unitaire</th>
                            <th width="15%">Montant</th>
                            <th width="15%">UOM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($devis['items'] as $index => $item)
                            <tr>
                                <td>
                                    <select name="items[{{ $index }}][item_code]" class="form-control select2-item">
                                        @foreach ($itemsList as $itemli)
                                            <option value="{{ $itemli['name'] }}" {{ $itemli['name'] == $item['item_code'] ? 'selected' : '' }}>
                                                {{ $itemli['item_name'] }} ({{ $itemli['name'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[{{ $index }}][item_code_originale]" value="{{ $item['item_code'] }}">
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][qty]" value="{{ $item['qty'] }}" step="0.01" class="form-control">
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][rate]" value="{{ $item['rate'] }}" step="0.01" class="form-control">
                                </td>
                                <td>
                                    {{ number_format($item['qty'] * $item['rate'], 2) }}
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $index }}][uom]" value="{{ $item['uom'] }}" class="form-control">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($devis['docstatus'] == 0)
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-success" onclick="return confirm('Êtes-vous sûr de vouloir mettre à jour et soumettre ce devis?')">
                    <i class="fas fa-check-circle"></i> Mettre à jour et Soumettre
                </button>
            </div>
            @endif
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-item').select2({
            width: '100%',
            dropdownParent: $('.items-table')
        });
        
        // Recalcul du montant quand la quantité ou le prix change
        $('table.items-table').on('change', 'input[name*="[qty]"], input[name*="[rate]"]', function() {
            const row = $(this).closest('tr');
            const qty = parseFloat(row.find('input[name*="[qty]"]').val()) || 0;
            const rate = parseFloat(row.find('input[name*="[rate]"]').val()) || 0;
            const amount = qty * rate;
            row.find('td:eq(3)').text(amount.toFixed(2));
        });
    });
</script>
@endpush
@endsection