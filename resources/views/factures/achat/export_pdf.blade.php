<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $facture['name'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #333; margin-bottom: 5px; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .info-card { width: 48%; }
        .info-card-title { background-color: #f2f2f2; padding: 5px; font-weight: bold; }
        .info-card-content { padding: 10px; }
        .info-item { display: flex; margin-bottom: 5px; }
        .info-label { width: 120px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f2f2f2; text-align: left; padding: 5px; }
        td { padding: 5px; border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; }
        .footer { margin-top: 30px; font-size: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Facture {{ $facture['name'] }}</h1>
    </div>

    <div class="info-section">
        <div class="info-card">
            <div class="info-card-title">Fournisseur</div>
            <div class="info-card-content">
                <div class="info-item">
                    <div class="info-label">Nom :</div>
                    <div>{{ $facture['supplier_name'] ?? $facture['supplier'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Entreprise :</div>
                    <div>{{ $facture['company'] }}</div>
                </div>
            </div>
        </div>
        
        <div class="info-card">
            <div class="info-card-title">Informations</div>
            <div class="info-card-content">
                <div class="info-item">
                    <div class="info-label">Statut :</div>
                    <div>{{ $facture['status'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date :</div>
                    <div>{{ \Carbon\Carbon::parse($facture['posting_date'])->format('d/m/Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total :</div>
                    <div>{{ number_format($facture['grand_total'], 2) }} {{ $facture['currency'] }}</div>
                </div>
            </div>
        </div>
    </div>

    @if (!empty($facture['items']))
    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="25%">Article</th>
                <th width="20%">Code</th>
                <th width="10%">Qté</th>
                <th width="10%">Unité</th>
                <th width="15%">Prix unitaire</th>
                <th width="15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($facture['items'] as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['item_name'] }}</td>
                <td>{{ $item['item_code'] }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>{{ $item['uom'] }}</td>
                <td class="text-right">{{ number_format($item['rate'], 2) }}</td>
                <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5"></td>
                <td class="text-right">Total :</td>
                <td class="text-right">{{ number_format($facture['grand_total'], 2) }} {{ $facture['currency'] }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    <div class="footer">
        Document généré automatiquement par le système
    </div>
</body>
</html>