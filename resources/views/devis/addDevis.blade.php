@extends('home')

@section('title', 'Formulaire de Devis')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h2 class="card-title">Données Disponibles</h2>
    </div>

    <div class="card-body">
        <div class="alert alert-info">
            Cette page affiche seulement les données disponibles. La création de devis n'est pas encore implémentée.
        </div>

        <div class="row">
            <!-- Fournisseurs -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        Fournisseurs ({{ count($suppliers) }})
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($suppliers as $supplier)
                            <li class="list-group-item">
                                <strong>{{ $supplier['name'] }}</strong>
                                <br>{{ $supplier['supplier_name'] ?? '' }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Articles -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        Articles ({{ count($items) }})
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($items as $item)
                            <li class="list-group-item">
                                <strong>{{ $item['name'] }}</strong>
                                <br>{{ $item['item_name'] ?? '' }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Entrepôts -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        Entrepôts ({{ count($warehouses) }})
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($warehouses as $warehouse)
                            <li class="list-group-item">
                                <strong>{{ $warehouse['name'] }}</strong>
                                <br>{{ $warehouse['warehouse_name'] ?? '' }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('devis.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>
</div>
@endsection