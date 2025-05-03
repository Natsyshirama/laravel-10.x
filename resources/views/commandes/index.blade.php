@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Liste des Commandes d'Achat</h1>

    {{-- Formulaire de filtre par fournisseur --}}
    <form method="GET" action="{{ route('commandes.index') }}" class="mb-4">
        <div class="form-group row">
            <label for="supplier" class="col-sm-2 col-form-label">Fournisseur</label>
            <div class="col-sm-6">
                <select name="supplier" id="supplier" class="form-control">
                    <option value="">-- Tous les fournisseurs --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier['name'] }}" {{ $selectedSupplier == $supplier['name'] ? 'selected' : '' }}>
                            {{ $supplier['supplier_name'] ?? $supplier['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-primary">Filtrer</button>
            </div>
        </div>
    </form>

    {{-- Tableau des commandes d'achat --}}
    @if (count($commandes) > 0)
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>N° Commande</th>
                    <th>Fournisseur</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($commandes as $commande)
                    <tr>
                        <td>{{ $commande['name'] }}</td>
                        <td>{{ $commande['supplier'] ?? $commande['supplier_name']  }}</td>
                        <td>{{ $commande['status'] }}</td>
                        <td>{{ $commande['transaction_date'] }}</td>
                        <td>{{ number_format($commande['grand_total'], 2) }}{{ $commande['currency'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">
            Aucune commande d'achat trouvée.
        </div>
    @endif
</div>
@endsection
