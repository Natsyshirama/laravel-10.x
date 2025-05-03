<div class="container">
    <h1 class="mb-4">Liste des Commandes d'Achat</h1>

    {{-- Formulaire de filtre par type (facturé / reçu) --}}
    <form method="GET" action="{{ route('commandes.index') }}" class="mb-4">
        <div class="form-row align-items-end">
            <div class="form-group col-md-4">
                <label for="type">Filtrer par :</label>
                <select name="type" id="type" class="form-control">
                    <option value="">-- Tous --</option>
                    <option value="paye" {{ $selectedType == 'paye' ? 'selected' : '' }}>Entièrement facturé</option>
                    <option value="non_paye" {{ $selectedType == 'non_paye' ? 'selected' : '' }}>Non facturé</option>
                    <option value="non_recu" {{ $selectedType == 'non_recu' ? 'selected' : '' }}>Non reçu</option>
                    <option value="recu" {{ $selectedType == 'recu' ? 'selected' : '' }}>Entièrement reçu</option>
                    <option value="paye_recu" {{ $selectedType == 'paye_recu' ? 'selected' : '' }}>Partiellement payé et reçu</option>
                </select>
            </div>
            <div class="form-group col-md-2">
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
