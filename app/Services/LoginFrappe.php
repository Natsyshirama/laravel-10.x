<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;  

class LoginFrappe{
public function login(string $email, string $password)
{
    $url = rtrim(config('frappe.api_base'), '/login');
   

    $response = Http::withHeaders([
        'Content-Type' => 'application/x-www-form-urlencoded',
    ])->asForm()->post($url, [
        'usr' => $email,
        'pwd' => $password,
    ]);

    if ($response->successful()) {
        $setCookie = $response->header('Set-Cookie');

        $sid = null;

        if ($setCookie && is_array($setCookie)) {
            foreach ($setCookie as $cookie) {
                if (Str::startsWith($cookie, 'sid=')) {
                    $sid = explode(';', substr($cookie, 4))[0]; // Extrait valuer sid
                    break;
                }
            }
        } elseif (is_string($setCookie) && Str::startsWith($setCookie, 'sid=')) {
            $sid = explode(';', substr($setCookie, 4))[0];
        }

        if ($sid) {
            return [
                'success' => true,
                'sid' => $sid,
                'full_name' => $response->json()['full_name'] ?? null,
            ];
        }
    }

    return [
        'success' => false,
        'message' => 'Identifiants invalides ou SID introuvable',
    ];
}
}