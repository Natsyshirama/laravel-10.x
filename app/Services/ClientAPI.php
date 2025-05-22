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
    
    public function ajouterClient( array $data){

        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté à ERPNext');
        }

        $ajoutReponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post($this->baseUrl. '/api/resource/Customer', [
            'data'=>$this->formatDataClient($data)
        ]);

        if (!$ajoutReponse->successful()){
            throw new \Exception("Erreur API Client : " . $ajoutReponse->body());

        }
        return $ajoutReponse->json('data');
    }

    public function formatDataClient(array $data){
        return [
            'customer_name' => $data['customer_name'],
            'customer_type' => $data['customer_type'] ?? 'Individual',
            'customer_group' => $data['customer_group'] ?? 'Commercial',
            'territory' => $data['territory'] ?? 'Madagascar',
            'mobile_no' => $data['mobile_no'] ?? null,
            'email_id' => $data['email_id'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'default_currency' => $data['default_currency'] ?? 'EUR',
            'default_price_list' => $data['default_price_list'] ?? 'Standard Selling',
            'language' => $data['language'] ?? 'fr',
            'website' => $data['website'] ?? null,
            'disabled' => $data['disabled'] ?? 0
        ];
    
    }
    public function getObjetSelection(){
        $sid = Session::get('sid');

        return[
            'customer_groups'=> $this->getCustomerGroup($sid),
            'territory'=> $this->getTerritory($sid),
            'default_price_list'=> $this->getDefaultPriceList($sid),
        ];
    }

    public function getCustomerGroup($sid){
        $reponse =Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Customer Group',[
            'fields' => json_encode(['name'])
        ]);
        if (!$reponse->successful()) {
            throw new \Exception("Erreur API Client : " . $reponse->body());
        }
        return $reponse->json('data');
    }

    public function getTerritory($sid){
        $reponse =Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Territory',[
            'fields' => json_encode(['name'])
        ]);
        if (!$reponse->successful()) {
            throw new \Exception("Erreur API Client : " . $reponse->body());
        }
        return $reponse->json('data');
    }
    public function getDefaultPriceList($sid){
        $reponse =Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Price List',[
            'fields' => json_encode(['name'])
        ]);
        if (!$reponse->successful()) {
            throw new \Exception("Erreur API Client : " . $reponse->body());
        }
        return $reponse->json('data');
    }
}