<?php

namespace App\Services;



use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;


class ClientAPI{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://erpnext.localhost:8000';
    }
    
    public function getAllClient(){
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $reponse =Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Customer',[
            'fields' => json_encode(['name', 'customer_group', 'territory','disabled'])
        ]);
        if (!$reponse->successful()) {
            throw new \Exception("Erreur API Fournisseur : " . $reponse->body());
        }
        return $reponse->json('data');

    }
    public function getClientDetails($name){
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $reponse =Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Customer/' . $name);
        if (!$reponse->successful()) {
            throw new \Exception("Erreur API Fournisseur : " . $reponse->body());
        }
        return $reponse->json('data');
    }
    public function desactiverClient($name){
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $reponse =Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->put($this->baseUrl . '/api/resource/Customer/' . $name, [
            'disabled' => 1
        ]);
        if (!$reponse->successful()) {
            throw new \Exception("Erreur API Client : " . $reponse->body());
        }
        return $reponse->json('data');
    }
    public function activerClient($name){
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $reponse =Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->put($this->baseUrl . '/api/resource/Customer/' . $name, [
            'disabled' => 0
        ]);
        if (!$reponse->successful()) {
            throw new \Exception("Erreur API Client : " . $reponse->body());
        }
        return $reponse->json('data');
    }
        
}