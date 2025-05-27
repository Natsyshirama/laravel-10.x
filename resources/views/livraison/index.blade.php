@extends('home')

@section('title', 'Livraison ')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> Livraison
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
            <h2 class="card-title">New livraison</h2>
            <a href="#" class="btn btn-outline-primary">
                <i class="fas fa-filter"></i> new livraison
            </a>
        </div>
        <form method="GET" action="{{ route('livraison.index') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="type" class="form-label">Statut de livraison</label>
                    <select name="status" id="type" class="form-control select2-status">
                        <option value="">-- Tous les statuts --</option>
                        <option value="a facturer" {{ $selectStatus == 'a facturer' ? 'selected' : '' }}>a facturer</option>
                        <option value="complet" {{ $selectStatus == 'complet' ? 'selected' : '' }}>complet</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>

        @if(count($livraions) > 0)
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
                        @foreach($livraions as $livraion)
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="{{ route('livraison.show', ['name' => $livraion['name']]) }}" class="text-primary">
                                        {{ $livraion['name'] }}
                                    </a>
                                </td>
                                <td>{{ $livraion['customer_name'] }}</td>
                                <td>
                                    <span class="badge 
                                        @if($livraion['status'] == 'odered') bg-success
                                        @elseif($livraion['status'] == 'draft') bg-danger
                                        @else bg-warning
                                        @endif">
                                        {{ $livraion['status'] }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($livraion['posting_date'])->format('d/m/Y') }}</td>
                                <td class="font-weight-bold">{{ number_format($livraion['total'], 2) }} {{ $livraion['currency'] }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="#" class="btn btn-info" title="Voir">
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
                <i class="fas fa-info-circle"></i>Aucun Livraison Trouvee
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