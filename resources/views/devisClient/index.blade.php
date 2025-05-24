@extends('home')

@section('title', 'Devis Client')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> Devis Client
            </h2>
            
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
    </div>

    <div class="card-body">
    <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">New Devis</h2>
            <a href="{{ route('devisClient.create') }}" class="btn btn-outline-primary">
                <i class="fas fa-filter"></i> New Devis
            </a>
        </div>
        <form method="GET" action="{{ route('devisClient.index') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="type" class="form-label">Statut de Devis</label>
                    <select name="status" id="type" class="form-control select2-status">
                        <option value="">-- Tous les statuts --</option>
                        <option value="ordered" {{ $selectStatus == 'rdered' ? 'selected' : '' }}>Ordered</option>
                        <option value="open" {{ $selectStatus == 'open' ? 'selected' : '' }}>open </option>
                        <option value="draft" {{ $selectStatus == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>

        @if(count($quotations) > 0)
            <div class="table-responsive">
                <table class="data-table table-hover">
                    <thead>
                        <tr>
                            <th>Id Devis</th>
                            <th>client</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotations as $quotation)
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="{{ route('devisClient.show', ['name' => $quotation['name']]) }}" class="text-primary">
                                        {{ $quotation['name'] }}
                                    </a>
                                </td>
                                <td>{{ $quotation['customer_name'] }}</td>
                                <td>
                                    <span class="badge 
                                        @if($quotation['status'] == 'odered') bg-success
                                        @elseif($quotation['status'] == 'draft') bg-danger
                                        @else bg-warning
                                        @endif">
                                        {{ $quotation['status'] }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($quotation['transaction_date'])->format('d/m/Y') }}</td>
                                <td class="font-weight-bold">{{ number_format($quotation['total'], 2) }} {{ $quotation['currency'] }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('devisClient.show', ['name' => $quotation['name']]) }}" class="btn btn-info" title="Voir">
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
                <i class="fas fa-info-circle"></i>Aucun Devis Trouve
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