<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserAPI;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    protected $userAPI;
    
    public function __construct(UserAPI $userAPI)
    {
        $this->userAPI = $userAPI;
    }

    public function showProfile()
    {
        $profile = $this->userAPI->getProfile();
        if (isset($profile['message']['success']) && $profile['message']['success']) {
            $user = $profile['message']['user'];
            return view('profile', compact('user'));
        }
        return redirect()->route('home')->withErrors(['message' => 'Erreur lors de la récupération du profil']);    }

   
}
