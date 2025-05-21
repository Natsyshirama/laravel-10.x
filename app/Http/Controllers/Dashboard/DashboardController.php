<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DashbordService;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    protected $dashbordService;

    public function __construct(DashbordService $dashbordService)
    {
        $this->dashbordService = $dashbordService;
    }
    public function achatsGlobal()
    {
        try {
            $stats = $this->dashbordService->getDashboard();
            return view('dashboard.dashbAchats', ['stats' => $stats]);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return view('dashboard.dashbAchats');
        }
    }
    
}
