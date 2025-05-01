<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base URL de l'API Frappe/ERPNext
    |--------------------------------------------------------------------------
    |
    | Cette URL sera utilisée comme base pour tous les appels vers l'API
    | de votre instance ERPNext. Elle doit inclure le chemin "/api/method".
    | Exemple : http://localhost:8000/api/method
    |
    */

    'api_base' => env('FRAPPE_API_BASE', 'http://localhost:8000/api/method'),

];
