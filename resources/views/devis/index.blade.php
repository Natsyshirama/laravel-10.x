<form method="GET" action="{{ route('devis.index') }}">
    <label for="supplier">Choisir un fournisseur :</label>
    <select name="supplier" id="supplier">
        <option value="">-- Tous --</option>
        @foreach($suppliers as $supplier)
            <option value="{{ $supplier['name'] }}" {{ $selectedSupplier == $supplier['name'] ? 'selected' : '' }}>
                {{ $supplier['name'] }}
            </option>
        @endforeach
    </select>
    <button type="submit">Filtrer</button>
</form>

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
