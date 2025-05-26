@extends('home')

@section('title', 'command Client')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> commande Client
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
            <h2 class="card-title">New Commande</h2>
            <a href="#" class="btn btn-outline-primary">
                <i class="fas fa-filter"></i> new commande
            </a>
        </div>
        <form method="GET" action="{{ route('comdClient.index') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="type" class="form-label">Statut de Commande</label>
                    <select name="status" id="type" class="form-control select2-status">
                        <option value="">-- Tous les statuts --</option>
                        <option value="facturee" {{ $selectStatus == 'facturee' ? 'selected' : '' }}>facturee</option>
                        <option value="livree" {{ $selectStatus == 'livree' ? 'selected' : '' }}>livree </option>
                        <option value="livree et facturee" {{ $selectStatus == 'livree et facturee' ? 'selected' : '' }}>livree et facturee</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>

        @if(count($commandeClients) > 0)
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
                        @foreach($commandeClients as $commandeClient)
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="{{ route('comdClient.show', ['name' => $commandeClient['name']]) }}" class="text-primary">
                                        {{ $commandeClient['name'] }}
                                    </a>
                                </td>
                                <td>{{ $commandeClient['customer_name'] }}</td>
                                <td>
                                    <span class="badge 
                                        @if($commandeClient['status'] == 'odered') bg-success
                                        @elseif($commandeClient['status'] == 'draft') bg-danger
                                        @else bg-warning
                                        @endif">
                                        {{ $commandeClient['status'] }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($commandeClient['transaction_date'])->format('d/m/Y') }}</td>
                                <td class="font-weight-bold">{{ number_format($commandeClient['total'], 2) }} {{ $commandeClient['currency'] }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('devisClient.show', ['name' => $commandeClient['name']]) }}" class="btn btn-info" title="Voir">
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
                <i class="fas fa-info-circle"></i>Aucun Commande Trouvee
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