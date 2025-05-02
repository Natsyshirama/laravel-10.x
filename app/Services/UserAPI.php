<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class UserAPI
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getLoggedUserId()
    {
        $sid = Session::get('sid');

        $response = Http::withHeaders([
            'Cookie' => "sid={$sid}",
        ])->get("{$this->baseUrl}/api/method/frappe.auth.get_logged_user");

        if ($response->successful()) {
            return $response->json()['message'];
        }

        return null;
    }

    public function getUserProfile($userId)
    {
        $sid = Session::get('sid');

        $response = Http::withHeaders([
            'Cookie' => "sid={$sid}",
        ])->get("{$this->baseUrl}/api/resource/User/{$userId}");

        if ($response->successful()) {
            return $response->json()['data'];
        }

        return null;
    }
}
