<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ClientAPI;

class ClientController extends Controller
{

    protected $clientApi;

    public function __construct(ClientAPI $clientApi)
    {
        $this->clientApi = $clientApi;
    }
    public function index(){
        try{
            $clients = $this->clientApi->getAllClient();
            return view('client.index', [
                'clients' => $clients
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function show($name){
        try{
            $client = $this->clientApi->getClientDetails($name);
            return view('client.show', [
                'client' => $client
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function activerClient($name){
        try{
             $this->clientApi->activerClient($name);
            return redirect()->back()->with('success', 'client reactiver');


        }catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function desactiverClient($name){
        try{
            $this->clientApi->desactiverClient($name);
            return redirect()->back()->with('success', 'client desactiver');

        }catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}
