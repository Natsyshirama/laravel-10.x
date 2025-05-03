@extends('home')

@section('title', 'Filtrer les Commandes')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-filter"></i> Filtrer les Commandes par Fournisseur
        </h2>
    </div>
    
    <div class="card-body">
        <form method="GET" action="{{ route('commandes.index') }}">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="supplier" class="form-label">Fournisseur</label>
                        <select name="supplier" id="supplier" class="form-control select2">
                            <option value="">-- Tous les fournisseurs --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier['name'] }}">
                                    {{ $supplier['supplier_name'] ?? $supplier['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-footer mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Afficher les Commandes
                </button>
                
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Sélectionnez un fournisseur",
            allowClear: true
        });
    });
</script>
@endpush