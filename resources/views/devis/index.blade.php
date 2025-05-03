
<div class="container">
    <h1 class="mb-4">Liste des Devis </h1>

    <a href="{{ route('devis.filtre') }}" class="btn btn-outline-primary mb-3">Filtrer par fournisseur</a>

    @if (count($commandes) > 0)
<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Fournisseur</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Total</th>
            <th>valable jusqu'à</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($devis as $devi)
            <tr>
            <td>
                <a href="{{ route('devis.show', ['name' => $devi['name']]) }}">
                    {{ $devi['name'] }}
                </a>  
            </td>              
            <td>{{ $devi['supplier_name'] ?? $devi['supplier'] }}</td>
                <td>{{ $devi['status'] }}</td>
                <td>{{ $devi['transaction_date'] }}</td>
                <td>{{ $devi['grand_total'] }} {{ $devi['currency'] }}</td>
                <td>{{ $devi['valid_till'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Aucun devis trouvé</td>
            </tr>
        @endforelse
    </tbody>
</table>
@else
        <div class="alert alert-info">
            Aucune devis  trouvée.
        </div>
    @endif
    </div>
