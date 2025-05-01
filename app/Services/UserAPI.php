<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class UserAPI
{
    protected $base;

    public function __construct()
    {
        // Utilise config() plutôt que env()
        $this->base = rtrim(config('frappe.api_base'), '/');
    }

    public function getProfile()
{
    $url = "{$this->base}/erpnext.user.getProfile";
    return Http::get($url)->json();
}

}