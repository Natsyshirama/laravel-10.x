
<div class="container">
    <h1>Détails de la Commande d'Achat : {{ $commande['name'] }}</h1>

    <a href="{{ route('commandes.index') }}" class="btn btn-secondary mb-3">← Retour à la liste</a>

    <div class="card mb-4">
        <div class="card-header">Informations générales</div>
        <div class="card-body">
            <p><strong>Fournisseur :</strong> {{ $commande['supplier'] ?? $commande['supplier_name'] }}</p>
            <p><strong>Entreprise :</strong> {{ $commande['company'] }}</p>
            <p><strong>Statut :</strong> {{ $commande['status'] }}</p>
            <p><strong>Date de transaction :</strong> {{ $commande['transaction_date'] }}</p>
            <p><strong>Total :</strong> {{ number_format($commande['grand_total'], 2) }} {{ $commande['currency'] }}</p>
        </div>
    </div>

    @if (!empty($commande['items']))
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
                    @foreach ($commande['items'] as $item)
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

