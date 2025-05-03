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
            <th>Qte</th>
            <th>Prix Unitaire</th>
            <th>UOM</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($devis['items'] as $item)
        <form action="{{ route('devis.update-item', ['name' => $devis['name']]) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="item_code_originale" value="{{ $item['item_code'] }}">

    <tr>
   
    <td>
    <select name="item_code">
    @foreach ($itemsList as $itemli)
        <option value="{{ $itemli['name'] }}" {{ $itemli['name'] == $item['item_code'] ? 'selected' : '' }}>
            {{ $itemli['item_name'] }} ({{ $itemli['name'] }})
        </option>
    @endforeach
</select>

    </td>
        <td><input type="number" name="qty" value="{{ $item['qty'] }}" step="0.01"></td>
        <td><input type="number" name="rate" value="{{ $item['rate'] }}" step="0.01"></td>
        <td><input type="text" name="uom" value="{{ $item['uom'] }}"></td>
        <td>
            <button type="submit" class="btn btn-sm btn-success">Mettre à jour</button>
        </td>
    </tr>
</form>

        @endforeach
    </tbody>
</table>