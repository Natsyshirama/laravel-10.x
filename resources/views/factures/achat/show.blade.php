
<div class="container">
    <h1>Détails de la facture d'Achat : {{ $facture['name'] }}</h1>

    <a href="{{ route('factures.achat.index') }}" class="btn btn-secondary mb-3">← Retour à la liste</a>

    <div class="card mb-4">
        <div class="card-header">Informations générales</div>
        <div class="card-body">
            <p><strong>Fournisseur :</strong> {{ $facture['supplier'] ?? $facture['supplier_name'] }}</p>
            <p><strong>Entreprise :</strong> {{ $facture['company'] }}</p>
            <p><strong>Statut :</strong> {{ $facture['status'] }}</p>
            <p><strong>Date de transaction :</strong> {{ $facture['posting_date'] }} - {{ $facture['posting_time'] }}</p>
            <p><strong>Total :</strong> {{ number_format($facture['grand_total'], 2) }} {{ $facture['currency'] }}</p>
        </div>
    </div>

    @if (!empty($facture['items']))
    <div class="card">
        <div class="card-header">Articles commandés</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Article</th>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Unité</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($facture['items'] as $item)
                    <tr>
                        <td>{{ $item['item_code'] }}</td>
                        <td>{{ $item['item_name'] }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>{{ $item['uom'] }}</td>
                        <td>{{ number_format($item['rate'], 2) }}</td>
                        <td>{{ number_format($item['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

