<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\UserModel;
use App\Models\FlatshareModel;

class Profil extends Controller
{
    protected object $userModel;
    protected object $flatshareModel;

    public function __construct($params)
    {
        $this->userModel = new UserModel();
        $this->flatshareModel = new FlatshareModel();
        parent::__construct($params);
    }

    public function getProfil()
    {
        // Récupérer l'ID utilisateur depuis les paramètres
        $userId = $this->params['id'];
        // Utiliser le modèle UserModel pour récupérer les données de l'utilisateur à partir de la base de données
        $user = $this->userModel->getUserById($userId);
        if ($user) {
            // Récupérer le code d'accès de la colocation associée
            $flatshare = $this->flatshareModel->getFlatshareByUserId($userId);
            $user['access_code'] = $flatshare['access_code'] ?? null;

            return $user;
        } else {
            http_response_code(404);
            return ['message' => 'User not found'];
        }
        exit;
    }

    public function putProfil()
    {
        $userId = $this->params['id'];
        $data = $this->body; 

        $updated = $this->userModel->putUser($userId, $data);

        if ($updated) {
            echo json_encode(['message' => 'Profile updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update profile']);
        }
        exit;
    }

    
}
