
<div class="container">
    <h1 class="mb-4">Liste des Factures d'Achat</h1>
    <form method="GET" action="{{ route('factures.achat.index') }}" class="mb-4">
        <div class="form-row align-items-end">
            <div class="form-group col-md-4">
                <label for="type">Filtrer par :</label>
                <select name="type" id="type" class="form-control">
                    <option value="">-- Tous --</option>
                    <option value="payer" {{ $selectType == 'payer' ? 'select' : '' }}>Payé</option>
                    <option value="non_payer" {{ $selectType == 'non_payer' ? 'select' : '' }}>Non Payé</option>
                    <option value="enretard" {{ $selectType == 'enretard' ? 'select' : '' }}>enretard</option>

                </select>
            </div>
            <div class="form-group col-md-2">
                <button type="submit" class="btn btn-primary">Filtrer</button>
            </div>
        </div>
    </form>
    @if(count($factures) > 0)
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>N° Facture</th>
                    <th>Fournisseur</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($factures as $facture)
                    <tr>
                        <td>
                    <a href="{{ route('factures.achat.show', ['name' => $facture['name']]) }}">
                            {{ $facture['name'] }}
                        </a>        
                    </td>
                        <td>{{ $facture['supplier'] }}</td>
                        <td>{{ $facture['status'] }}</td>
                        <td>{{ $facture['posting_date'] }}</td>
                        <td>{{ number_format($facture['grand_total'], 2) }} {{ $facture['currency'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">Aucune facture trouvée.</div>
    @endif
</div>