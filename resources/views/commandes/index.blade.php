<div class="container">
    <h1 class="mb-4">Liste des Commandes d'Achat</h1>

    <a href="{{ route('commandes.filtre') }}" class="btn btn-outline-primary mb-3">Filtrer par fournisseur</a>

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
                        <td>
                            <a href="{{ route('commandes.show', ['name' => $commande['name']]) }}">
                                {{ $commande['name'] }}
                            </a>
                        </td>
                        <td>{{ $commande['supplier'] ?? $commande['supplier_name'] }}</td>
                        <td>{{ $commande['status'] }}</td>
                        <td>{{ $commande['transaction_date'] }}</td>
                        <td>{{ number_format($commande['grand_total'], 2) }} {{ $commande['currency'] }}</td>
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
