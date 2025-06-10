<?php

namespace App\Http\Controllers\Reset;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
class ResetController extends Controller
{
    
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function resetAll()
    {
        $sid = Session::get('sid');
        if (!$sid) {
            return response()->json(['success' => false, 'message' => 'Non connecte'], 401);
        }

        $doctypes = [
            "Employee",
            "Salary Component",
            "Salary Structure",
            "Salary Detail",
            "Salary Structure Assignment",
            "Salary Slip",
            "Income Tax Slab",
            "Payroll Entry"
        ];

        try {
            $response = Http::timeout(120)
                ->withHeaders([
                    'Cookie' => 'sid=' . $sid,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/api/method/erpnext.donne.page.resetdata.reset.reinitialiser_donnees', [
                    'doctypes' => json_encode($doctypes),
                ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur HTTP lors de la requête à Frappe.',
                    'details' => $response->body()
                ], 500);
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Erreur lors de la donnee', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Exception lors de appel Frappe: ' . $e->getMessage()
            ], 500);
        }
    }

}
