<h2>Détails du devis : {{ $devis['name'] }}</h2>
<p><strong>Fournisseur :</strong> {{ $devis['supplier'] }}</p>
<p><strong>Date :</strong> {{ $devis['transaction_date'] }}</p>
<p><strong>Valable jusqu'au :</strong> {{ $devis['valid_till'] }}</p>
<p><strong>Montant total :</strong> {{ $devis['grand_total'] }} {{ $devis['currency'] }}</p>
<p><strong>Statut :</strong> {{ $devis['status'] }}</p>

<form action="{{ route('devis.update', ['name' => $devis['name']]) }}" method="POST">
    @csrf
    @method('PUT')
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
            </tr>
        </thead>
        <tbody>
            @foreach ($devis['items'] as $index => $item)
                <tr>
                    <td>
                        <input type="text" name="items[{{ $index }}][item_code]" value="{{ $item['item_code'] }}" readonly>
                        <input type="hidden" name="items[{{ $index }}][item_name]" value="{{ $item['item_name'] }}">
                    </td>
                    <td><input type="text" name="items[{{ $index }}][description]" value="{{ $item['description'] }}"></td>
                    <td><input type="number" step="0.01" name="items[{{ $index }}][qty]" value="{{ $item['qty'] }}"></td>
                    <td><input type="number" step="0.01" name="items[{{ $index }}][rate]" value="{{ $item['rate'] }}"></td>
                    <td><input type="text" name="items[{{ $index }}][uom]" value="{{ $item['uom'] }}"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <button type="submit">Mettre à jour les prix</button>
</form>
