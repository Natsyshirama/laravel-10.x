<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Services\FrappeAPI;

class AuthController extends Controller
{

    protected $frappe;
    protected $login;

    public function __construct(FrappeAPI $frappe)
    {
        $this->frappe = $frappe;
    }

   
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $response = $this->frappe->login($request->email, $request->password);
    
        if ($response['success']) {
            Session::put('sid', $response['sid']);
            Session::put('user_name', $response['full_name']);
            return redirect()->route('home');
        }
        logger()->info('Session SID : ' . Session::get('sid'));

        return back()->withErrors(['message' => $response['message'] ?? 'Erreur inconnue']);
    }


        public function logout()
    {
        $response = Http::post(config('frappe.api_base') . '/erpnext.auth.logout');
        Session::flush();
        return redirect()->route('login');
    }

    public function home()
    {
        $user = Session::get('user');
        return view('home', compact('user'));
    }
}
