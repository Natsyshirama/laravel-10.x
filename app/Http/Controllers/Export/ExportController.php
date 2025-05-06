<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FactureAchatAPI;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    protected $factureAchatApi;

    public function __construct(FactureAchatAPI $factureAchatApi)
    {
        $this->factureAchatApi = $factureAchatApi;
    }

   
public function exportSinglePdf($name)
{
    try {
        $facture = $this->factureAchatApi->getFacturesAchatDetails($name);

        $pdf = Pdf::loadView('factures.achat.export_pdf', [
            'facture' => $facture
        ]);

        return $pdf->download('facture_'.$name.'_'.date('YmdHis').'.pdf');
        
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
}

public function exportSingleCsv($name)
{
    try {
        $facture = $this->factureAchatApi->getFacturesAchatDetails($name);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="facture_'.$name.'_'.date('YmdHis').'.csv"',
        ];

        $callback = function() use ($facture) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['N° Facture', $facture['name']]);
            fputcsv($file, ['Fournisseur', $facture['supplier_name'] ?? $facture['supplier']]);
            fputcsv($file, ['Date', $facture['posting_date']]);
            fputcsv($file, ['Statut', $facture['status']]);
            fputcsv($file, ['Total', $facture['grand_total'].' '.$facture['currency']]);
            fputcsv($file, []); // Ligne vide
            
            // tete articles
            fputcsv($file, [
                'Article', 'Code', 'Description', 'Quantité', 'Unité', 
                'Prix unitaire', 'Montant'
            ]);

            // Articles
            foreach ($facture['items'] as $item) {
                fputcsv($file, [
                    $item['item_name'],
                    $item['item_code'],
                    $item['description'] ?? '',
                    $item['qty'],
                    $item['uom'],
                    $item['rate'],
                    $item['amount']
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);

    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
}
}