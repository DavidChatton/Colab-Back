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

        // Vérifier l'URL et appeler la méthode joinFlatshareByPost si l'URL correspond
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        if ($uri === 'flatshare/join' && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
            echo json_encode($this->joinFlatshareByPost());
            exit;
        }
    }

    protected function generateAccessCode($length = 6)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[rand(0, strlen($chars) - 1)];
        }
        var_dump("Generated Access Code: " . $result); // Ajouter var_dump ici
        return $result;
    }

    protected function postFlatshare()
    {
        var_dump("postFlatshare called", $this->body);

        if (isset($this->body['name']) 
            && isset($this->body['address']) 
            && isset($this->body['user_id'])) {

            $this->body['access_code'] = $this->generateAccessCode();
            var_dump("Verification Generated access code: ", $this->body['access_code']);

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

    protected function joinFlatshareByPost()
    {
        var_dump("joinFlatshareByPost called", $this->body);
    
        if (isset($this->body['flatshareName'])
           && isset($this->body['access_code'])
           && isset($this->body['user_id']))
           {
            $flatshare = $this->flatshareModel->getFlatshareByNameAndCode($this->body['flatshareName'], $this->body['access_code']);
    
            var_dump("Flatshare retrieved:", $flatshare);
    
            if ($flatshare) {
                // Vérifier si l'ID de l'utilisateur existe dans la table users
                $user = $this->flatshareModel->getUserById($this->body['user_id']);
    
                if ($user) {
                    $this->flatshareModel->addFlatmate($this->body['user_id'], $flatshare['id'], date('Y-m-d H:i:s'));
    
                    var_dump("Flatmate added:", $this->body['user_id'], $flatshare['id']);
    
                    http_response_code(200);
                    return ['message' => 'Successfully joined flatshare'];
                } else {
                    http_response_code(404);
                    return ['message' => 'User not found'];
                }
            } else {
                http_response_code(404);
                return ['message' => 'Flatshare not found'];
            }
        } else {
            var_dump("Missing required fields", $this->body);
            http_response_code(400);
            return ['message' => 'Missing required fields'];
        }
    }
}
