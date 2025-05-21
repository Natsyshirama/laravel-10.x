
<?php


///Service :Tsy ampiasaina

public function cloneQuotation($originalName)
    {
        $sid = Session::get('sid');
        
        // 1. Récupérer le devis original avec tous les champs nécessaires
        $original = $this->getQuotationDetails($originalName);
    
        // 2. Préparer les données avec tous les champs obligatoires
        $data = [
            'doctype' => 'Supplier Quotation',
            'supplier' => $original['supplier'],
            'supplier_name' => $original['supplier_name'] ?? $original['supplier'],
            'company' => $original['company'],
            'transaction_date' => date('Y-m-d'),
            'valid_till' => date('Y-m-d', strtotime('+30 days')),
            'currency' => $original['currency'],
            'conversion_rate' => $original['conversion_rate'] ?? 1.0,
            'naming_series' => $original['naming_series'] ?? 'PUR-SQTN-.YYYY.-',
            'price_list' => $original['price_list'] ?? 'Standard Buying',
            'buying_price_list' => $original['buying_price_list'] ?? 'Standard Buying',
            'items' => array_map(function($item) {
                return [
                    'item_code' => $item['item_code'],
                    'item_name' => $item['item_name'] ?? $item['item_code'],
                    'description' => $item['description'] ?? '',
                    'qty' => $item['qty'],
                    'rate' => $item['rate'],
                    'uom' => $item['uom'],
                    'stock_uom' => $item['stock_uom'] ?? $item['uom'],
                    'conversion_factor' => $item['conversion_factor'] ?? 1.0,
                    'amount' => $item['qty'] * $item['rate']
                ];
            }, $original['items']),
            'amended_from' => $originalName,
            'docstatus' => 0 // Explicitement mettre en brouillon
        ];
    
        // 3. Créer le nouveau devis
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl."/api/resource/Supplier Quotation", $data);
    
        if (!$response->successful()) {
            throw new \Exception("Échec de la création du devis: ".$response->body());
        }
    
        return $response->json()['data']['name'];
    }
    
    protected function generateNewQuotationName($namingSeries)
    {
        $sid = Session::get('sid');
        
        // Récupérer le dernier numéro de la série
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl."/api/method/frappe.model.naming.set_new_name", [
            'doctype' => 'Supplier Quotation',
            'naming_series' => $namingSeries
        ]);
    
        if ($response->successful()) {
            return $response->json('message')['name'];
        }
    
        // Fallback si l'API échoue
        return $namingSeries . date('YmdHis');
    }
    public function updateQuotationItems($quotationName, array $items)
    {
        $sid = Session::get('sid');

        // Vérifie si le devis est annulé
        $quotation = $this->getQuotationDetails($quotationName);
        if ($quotation['docstatus'] == 2) {
            throw new \Exception("Impossible de modifier le devis '$quotationName' car il est annulé.");
        }

        $data = [
            'items' => $items
        ];

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->put($this->baseUrl . "/api/resource/Supplier Quotation/{$quotationName}", $data);

        if (!$response->successful()) {
            throw new \Exception("Erreur mise à jour : " . $response->body());
        }

        return $response->json('data');
    }

    public function submitQuotation($quotationName)
    {
        $sid = Session::get('sid');
        
        // 1. D'abord sauvegarder le document pour s'assurer qu'il est valide
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json'
        ])->put($this->baseUrl."/api/resource/Supplier Quotation/".$quotationName, [
            'docstatus' => 0
        ]);
    
        if (!$response->successful()) {
            throw new \Exception("Échec de la sauvegarde pré-soumission: ".$response->body());
        }
    
        // 2. Obtenir les dernières données avec le timestamp
        $quotation = $this->getQuotationDetails($quotationName);
    
        // 3. Préparer les données de soumission
        $submitData = [
            'doctype' => 'Supplier Quotation',
            'name' => $quotationName,
            'modified' => $quotation['modified']
        ];
    
        // 4. Soumettre le document
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl."/api/method/frappe.client.submit", [
            'doc' => json_encode($submitData)
        ]);
    
        if (!$response->successful()) {
            throw new \Exception("Échec de la soumission: ".$response->body());
        }
    
        return true;
    }
    //version submit Trotra ts mbol valider le copie
    // public function submitQuotation($quotationName)
    // {
    //     $sid = Session::get('sid');
    
    //     // D'abord rafraîchir le document
    //     $quotation = $this->getQuotationDetails($quotationName);
    
    //     // Ensuite soumettre le devis
    //     $response = Http::withHeaders([
    //         'Cookie' => 'sid=' . $sid
    //     ])->post($this->baseUrl . "/api/method/frappe.client.submit", [
    //         'doc' => json_encode([
    //             'doctype' => 'Supplier Quotation',
    //             'name' => $quotationName,
    //             'modified' => $quotation['modified'] // Inclure le timestamp actuel
    //         ])
    //     ]);
    
    //     if (!$response->successful()) {
    //         throw new \Exception("Erreur soumission : " . $response->body());
    //     }
    
    //     return $response->json('message');
    // }
    public function cancelQuotation($quotationName)
    {
        $sid = Session::get('sid');
    
        $quotation = $this->getQuotationDetails($quotationName);
        if ($quotation['docstatus'] == 2) {
            throw new \Exception("Le devis '$quotationName' est déjà annulé.");
        }
    
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->post($this->baseUrl . "/api/method/frappe.client.cancel", [
            'doctype' => 'Supplier Quotation',
            'name' => $quotationName
        ]);
    
        if (!$response->successful()) {
            throw new \Exception("Erreur annulation : " . $response->body());
        }
    
        return $response->json('message');
    }
    public function updateItemRate($quotationName, $itemCode, $newRate)
    {
        $sid = Session::get('sid');
        
        try {
            // 1. Récupérer le devis original avec tous les champs
            $original = $this->getQuotationDetails($quotationName);
    
            // 2. Vérifier les champs obligatoires
            $requiredFields = [
                'naming_series' => $original['naming_series'] ?? 'PUR-SQTN-.YYYY.-',
                'supplier' => $original['supplier'] ?? throw new \Exception("Champ supplier manquant"),
                'company' => $original['company'] ?? throw new \Exception("Champ company manquant"),
                'currency' => $original['currency'] ?? throw new \Exception("Champ currency manquant"),
                'conversion_rate' => $original['conversion_rate'] ?? 1.0
            ];
    
            // 3. Préparer les items avec validation
            $items = [];
            $itemFound = false;
            
            foreach ($original['items'] as $item) {
                if (!isset($item['item_code'], $item['qty'], $item['uom'])) {
                    throw new \Exception("Champs item_code, qty ou uom manquants dans un article");
                }
                
                $rate = ($item['item_code'] == $itemCode) ? (float)$newRate : (float)$item['rate'];
                $amount = (float)$item['qty'] * $rate;
                
                $items[] = [
                    'item_code' => $item['item_code'],
                    'item_name' => $item['item_name'] ?? $item['item_code'],
                    'description' => $item['description'] ?? '',
                    'qty' => (float)$item['qty'],
                    'rate' => $rate,
                    'uom' => $item['uom'],
                    'amount' => $amount
                ];
                
                if ($item['item_code'] == $itemCode) {
                    $itemFound = true;
                }
            }
            
            if (!$itemFound) {
                throw new \Exception("Article $itemCode non trouvé");
            }
    
            // 4. Préparer les données complètes
            $data = [
                'doctype' => 'Supplier Quotation',
                'naming_series' => $requiredFields['naming_series'],
                'supplier' => $requiredFields['supplier'],
                'supplier_name' => $original['supplier_name'] ?? $original['supplier'],
                'company' => $requiredFields['company'],
                'transaction_date' => date('Y-m-d'),
                'valid_till' => date('Y-m-d', strtotime('+30 days')),
                'currency' => $requiredFields['currency'],
                'conversion_rate' => $requiredFields['conversion_rate'],
                'price_list' => $original['price_list'] ?? 'Standard Buying',
                'items' => $items,
                'docstatus' => 0
            ];
    
            // 5. Si original est annulé, ajouter amended_from
            if ($original['docstatus'] == 2) {
                $data['amended_from'] = $quotationName;
            }
    
            // 6. Créer le nouveau devis
            $response = Http::withHeaders([
                'Cookie' => 'sid=' . $sid,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl."/api/resource/Supplier Quotation", $data);
    
            if (!$response->successful()) {
                throw new \Exception("Échec création: ".$response->body());
            }
    
            $newName = $response->json()['data']['name'];
    
            // 7. Soumettre le nouveau devis
            $submitResponse = Http::withHeaders([
                'Cookie' => 'sid=' . $sid,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl."/api/method/frappe.client.submit", [
                'doc' => json_encode([
                    'doctype' => 'Supplier Quotation',
                    'name' => $newName
                ])
            ]);
    
            if (!$submitResponse->successful()) {
                throw new \Exception("Échec soumission: ".$submitResponse->body());
            }
    
            return [
                'success' => true,
                'message' => 'Devis mis à jour avec succès',
                'new_quotation_name' => $newName
            ];
    
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }



    //Controller Update

public function update(Request $request, $name)
{
    try {
        $original = $this->devisApi->getQuotationDetails($name);
        $docstatus = $original['docstatus'];

        if ($docstatus == 0) {
            // Cas brouillon
            $items = $this->prepareItems($request->input('items'));
            $this->devisApi->updateQuotationItems($name, $items);
            $this->devisApi->submitQuotation($name);
            $newName = $name;
        } elseif ($docstatus == 1) {
            // Cas soumis
            // 1. Annuler l'original
            $this->devisApi->cancelQuotation($name);
            
            // 2. Créer la copie
            $newName = $this->devisApi->cloneQuotation($name);
            
            // 3. Mettre à jour si nécessaire
            if ($request->has('items')) {
                $items = $this->prepareItems($request->input('items'));
                $this->devisApi->updateQuotationItems($newName, $items);
            }
            
            // 4. Soumettre directement
            $this->devisApi->submitQuotation($newName);
        }

        return redirect()->route('devis.show', $newName)
            ->with('success', 'Devis mis à jour avec succès');

    } catch (\Exception $e) {
        return back()->withInput()->withErrors(['error' => $e->getMessage()]);
    }
}

protected function prepareItems($inputItems)
{
    return array_map(function($item) {
        return [
            'item_code' => $item['item_code'],
            'item_name' => $item['item_name'],
            'description' => $item['description'],
            'qty' => (float) $item['qty'],
            'rate' => (float) $item['rate'],
            'uom' => $item['uom'],
            'amount' => (float) $item['qty'] * (float) $item['rate']
        ];
    }, $inputItems);
}
public function updateItemRate(Request $request, $name)
{
    $request->validate([
        'item_code' => 'required|string',
        'new_rate' => 'required|numeric|min:0'
    ]);

    $result = $this->devisApi->updateItemRate(
        $name,
        $request->input('item_code'),
        $request->input('new_rate')
    );

    if ($result['success']) {
        return redirect()->route('devis.show', $result['new_quotation_name'])
            ->with('success', $result['message']);
    } else {
        return back()->withErrors(['error' => $result['message']]);
    }
}