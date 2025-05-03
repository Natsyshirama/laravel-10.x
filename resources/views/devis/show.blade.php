<h2>Détails du devis : {{ $devis['name'] }}</h2>
<p><strong>Fournisseur :</strong> {{ $devis['supplier'] }}</p>
<p><strong>Date :</strong> {{ $devis['transaction_date'] }}</p>
<p><strong>Valable jusqu'au :</strong> {{ $devis['valid_till'] ?? 'Non spécifié' }}</p>
<p><strong>Montant total :</strong> {{ $devis['grand_total'] }} {{ $devis['currency'] }}</p>
<p><strong>Statut :</strong> {{ $devis['status'] }}</p>

@if ($errors->any())
<div style="background: #f8d7da; color: #842029; padding: 15px; border: 1px solid #f5c2c7; border-radius: 5px; margin-bottom: 20px;">
    <strong>Erreur :</strong>
    <ul style="margin: 0; padding-left: 20px;">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Item</th>
            <th>Description</th>
            <th>Qte</th>
            <th>Prix Unitaire</th>
            <th>UOM</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($devis['items'] as $item)
            <tr>
                <td>{{ $item['item_code'] }}</td>
                <td>{{ $item['description'] }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>{{ $item['rate'] }}</td>
                <td>{{ $item['uom'] }}</td>
                <td>
                    <form action="{{ route('devis.update-rate', ['name' => $devis['name']]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="item_code" value="{{ $item['item_code'] }}">
                        <input type="number" step="0.01" name="new_rate" value="{{ $item['rate'] }}" style="width: 80px;">
                        <button type="submit" class="btn btn-sm btn-primary">Mettre à jour</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>