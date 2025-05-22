<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ClientAPI;
use Illuminate\Auth\Events\Validated;

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

    public function createForm(){
        try{
            $options = $this->clientApi->getObjetSelection();
            return view('client.createForm', [
                'options' => $options
            ]);

        }catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function addClient(Request $request){
        $request -> validate([
            'customer_name' => 'required|string|max:255',
            'email_id' => 'nullable|email',
            'mobile_no' => 'nullable|string'
            
        ]);
        try{
            $client = $this->clientApi->ajouterClient($request->all());
            return redirect()
            ->route('client.show', $client['name'])
            ->with('success', 'Client créé avec succès');
        }catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }
}
