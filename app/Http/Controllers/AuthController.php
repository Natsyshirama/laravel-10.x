<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Services\FrappeAPI;

class AuthController extends Controller
{

    protected $frappe;

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
        $response = $this->frappe->post('erpnext.auth.login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);
        
         // pour voir ce que renvoie réellement ERPNext        

        if (isset($response['message']['success']) && $response['message']['success']) {
            Session::put('user', $response['message']['user']);
            Session::put('api_key', $response['message']['api_key']);
            return redirect()->route('home');
        }

        return back()->withErrors(['message' => 'Identifiants invalides']);
    }
    public function logout()
    {
        Http::post(env('FRAPPE_API_BASE') . '/erpnext.auth.logout');
        Session::flush();
        return redirect()->route('login');
    }

    public function home()
    {
        $user = Session::get('user');
        return view('home', compact('user'));
    }
}
