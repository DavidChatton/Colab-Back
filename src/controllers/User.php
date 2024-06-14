<?php

namespace App\Controllers;

use App\Models\UserModel;

class User extends Controller {
    protected object $user;

    public function __construct($param) 
    {
        $this->user = new UserModel();
        parent::__construct($param);
    }

    public function getUser() {
        try {
            // Valider l'entrée
            $flatshareId = $this->params['flatshare_id'] ?? null; // Assurez-vous que l'ID est bien extrait des paramètres
            if (!$this->validateInput($flatshareId)) {
                header('HTTP/1.1 400 Bad Request');
                return ['message' => 'Invalid flatshare ID'];
            }

            // Récupérer les utilisateurs par colocation
            $users = $this->user->getUsersByFlatshare($flatshareId);
            
            // Retourner un code de réponse HTTP 200 pour une demande réussie
            header('HTTP/1.1 200 OK');
            return $users;
        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            return ['message' => 'Failed to fetch users', 'error' => $e->getMessage()];
        }
    }

    private function validateInput($flatshareId) {
        // Valider que l'ID de la colocation est un entier positif
        if (!is_numeric($flatshareId) || $flatshareId <= 0) {
            error_log("Invalid flatshare ID: " . $flatshareId);
            return false;
        }
        return true;
    }
}
