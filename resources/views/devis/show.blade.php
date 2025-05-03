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
                        <span class="info-value">{{ $devis['supplier'] }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date :</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($devis['transaction_date'])->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Statut :</span>
                        <span class="info-value badge 
                            @if($devis['status'] == 'Validé') bg-success
                            @elseif($devis['status'] == 'En attente') bg-warning
                            @else bg-secondary
                            @endif">
                            {{ $devis['status'] }}
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

        <div class="table-responsive">
            <table class="table table-bordered items-table">
                <thead class="bg-light">
                    <tr>
                        <th width="40%">Article</th>
                        <th width="15%">Quantité</th>
                        <th width="15%">Prix Unitaire</th>
                        <th width="10%">UOM</th>
                        <th width="20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($devis['items'] as $item)
                    <form action="{{ route('devis.update-item', ['name' => $devis['name']]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="item_code_originale" value="{{ $item['item_code'] }}">

                        <tr>
                            <td>
                                <select name="item_code" class="form-control select2-item">
                                    @foreach ($itemsList as $itemli)
                                        <option value="{{ $itemli['name'] }}" {{ $itemli['name'] == $item['item_code'] ? 'selected' : '' }}>
                                            {{ $itemli['item_name'] }} ({{ $itemli['name'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="qty" value="{{ $item['qty'] }}" step="0.01" class="form-control">
                            </td>
                            <td>
                                <input type="number" name="rate" value="{{ $item['rate'] }}" step="0.01" class="form-control">
                            </td>
                            <td>
                                <input type="text" name="uom" value="{{ $item['uom'] }}" class="form-control">
                            </td>
                            <td class="text-center">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-save"></i> Mettre à jour
                                </button>
                            </td>
                        </tr>
                    </form>
                    @endforeach
                </tbody>
            </table>
        </div>
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
    });
</script>
@endpush
@endsection