<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\FlatshareModel;

class Flatshare extends Controller {
    protected object $flatshareModel;

    public function __construct($params)
    {
        $this->flatshareModel = new FlatshareModel();
        parent::__construct($params);
    }

    protected function generateAccessCode($length = 6)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $result = '';
        for ($i = 0; $i < $length; $i += 1) {
            $result .= $chars[rand(0, strlen($chars) - 1)];
        }
        var_dump("Generated Access Code: " . $result); // Ajouter var_dump ici
        return $result;
    }

    protected function postFlatshare()
    {
        var_dump($this->body);

        if (isset($this->body['name']) 
            && isset($this->body['address']) 
            && isset($this->body['user_id'])) {

            $this->body['access_code'] = $this->generateAccessCode();
            var_dump("Verification Generated  access code: ", $this->body['access_code']);

            $this->body['creation_date_flatshare'] = date('Y-m-d H:i:s');
            var_dump("Creation date: ", $this->body['creation_date_flatshare']);

            // Créer la colocation et récupérer son ID
            $flatshareId = $this->flatshareModel->create($this->body);

            if ($flatshareId) {
                // Ajouter l'utilisateur en tant que colocataire
                $userId = $this->body['user_id'];
                $addedFlatmate = $this->flatshareModel->addFlatmate($userId, $flatshareId);
        
                if ($addedFlatmate) {
                    http_response_code(201);
                    return ['message' => 'Flatshare created successfully'];
                } else {
                    http_response_code(500);
                    return ['message' => 'Error adding flatmate'];
                }
            } else {
                http_response_code(500);
                return ['message' => 'Error creating flatshare'];
            }
        } else {
            var_dump("Missing required fields: ", $this->body);
            http_response_code(400);
            return ['message' => 'Missing required fields'];
        }
    }
}
