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

    public function show()
    {
        $userId = $this->userAPI->getLoggedUserId();

        if (!$userId) {
            return redirect()->route('login')->withErrors(['message' => 'Utilisateur non connecté']);
        }

        $profile = $this->userAPI->getUserProfile($userId);

        if (!$profile) {
            return redirect()->route('home')->withErrors(['message' => 'Impossible de charger le profil utilisateur']);
        }

        return view('profile', compact('profile'));
    }
}
