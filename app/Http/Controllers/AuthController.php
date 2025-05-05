<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $baseUrl;
    
    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }
    
    /**
     * Vérifie si l'utilisateur est authentifié
     */
    protected function checkAuth()
    {
        if (!Session::has('sid')) {
            return false;
        }
        return true;
    }
    
    /**
     * Afficher le formulaire de connexion
     */
    public function showLogin()
    {
        return view('login');
    }
    
    /**
     * Gérer la tentative de connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'usr' => 'required',
            'pwd' => 'required',
        ]);
        
        try {
            $response = Http::post($this->baseUrl . '/api/method/login', [
                'usr' => $request->usr,
                'pwd' => $request->pwd,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();

                $cookies = $response->cookies();
                $sidCookie = collect($cookies)->first(function ($cookie) {
                    return $cookie->getName() === 'sid';
                });
                
                if ($sidCookie) {
                    $sid = $sidCookie->getValue();

                    Session::put('sid', $sid);
                    Session::put('user_full_name', $data['full_name'] ?? $request->usr);
                    $cookie = cookie('sid', $sid, 120); // 120 minutes

                    return redirect()->route('dashboard.achats')->with('success', 'Connexion réussie')
                                                        ->cookie($cookie);          
                }
                
                return back()->withErrors(['message' => 'Erreur dans la récupération du cookie d\'authentification']);
            }
            
            return back()->withErrors(['message' => 'Identifiants incorrects']);
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erreur de connexion au serveur: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Déconnecter l'utilisateur
     */
    public function logout()
    {
        // Vérification de l'authentification
        if (!$this->checkAuth()) {
            return redirect()->route('login');
        }
        
        try {
            // Si SID est stocké en session
            if (Session::has('sid')) {
                $sid = Session::get('sid');
                
                // Appel à l'API de déconnexion
                $response = Http::withHeaders([
                    'Cookie' => 'sid=' . $sid
                ])->get($this->baseUrl . '/api/method/logout');
                
                // Supprimer les données de session
                Session::forget('sid');
                Session::forget('user_full_name');
            }
            
            return redirect()->route('login')->with('success', 'Vous avez été déconnecté');
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erreur lors de la déconnexion: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Récupérer l'utilisateur connecté actuel
     */
    public function getLoggedUser()
    {
        if (!$this->checkAuth()) {
            return response()->json(['error' => 'Non connecté'], 401);
        }
        
        try {
            $sid = Session::get('sid');
            
            // Appel à l'API pour obtenir l'utilisateur connecté
            $response = Http::withHeaders([
                'Cookie' => 'sid=' . $sid
            ])->get($this->baseUrl . '/api/method/frappe.auth.get_logged_user');
            
            if ($response->successful()) {
                $user = $response->json();
                return view('user.logged', compact('sid', 'user'));
            }
            
            // Si problème d'authentification, supprimer la session
            Session::forget('sid');
            Session::forget('user_full_name');
            
            return redirect()->route('login')->withErrors(['message' => 'Session expirée']);

       } catch (\Exception $e) {
        return view('user.logged', [
            'sid' => $sid ?? null,
            'user' => null,
            'error' => $e->getMessage()
        ]);
    }
    }
    
    /**
     * Dashboard après connexion
     */
    public function dashboard()
    {
        if (!$this->checkAuth()) {
            return redirect()->route('login');
        }
        
        $userName = Session::get('user_full_name', 'Utilisateur');
        
        return view('home', compact('userName'));
    }
}