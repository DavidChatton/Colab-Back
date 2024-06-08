<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\UserModel;

class Registration extends Controller {
  protected object $user;

  public function __construct($param) 
  {
    $this->user = new UserModel();
    parent::__construct($param);
  }

  public function postRegistration() {
    // Valider les entrées du formulaire
    if (!$this->validateInput($this->body)) {
      // Retourner un code de réponse HTTP 400 pour une demande incorrecte
      header('HTTP/1.1 400 Bad Request');
      error_log("Invalid input");
      return [
        'message' => 'Invalid input'
      ];
    }

    // Créer un utilisateur en utilisant les données du formulaire
    $result = $this->user->createUser($this->body);

    if ($result) {
      // Récupérer l'ID du dernier utilisateur inséré
      $userId = $this->user->getLastInsertedId();
      // Retourner un code de réponse HTTP 201 pour une création réussie
      header('HTTP/1.1 201 Created');
      error_log("User registered successfully with ID: $userId");
      return [
        'message' => 'User registered successfully',
        'user_id' => $userId
      ];
    }

    // Retourner un code de réponse HTTP 500 pour une erreur interne du serveur
    header('HTTP/1.1 500 Internal Server Error');
    error_log("Failed to register user");
    return [
      'message' => 'Failed to register user'
    ];
  }

  private function validateInput($input) {
    // Vérifier si toutes les clés nécessaires sont présentes dans les données d'entrée
    if (!isset($input['firstname'], $input['lastname'], $input['email'], $input['password'])) {
      error_log("Missing required fields");
      return false;
    }

    // Valider le format de l'adresse e-mail
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
      error_log("Invalid email format: " . $input['email']);
      return false;
    }

    // Vérifier la longueur du mot de passe
    if (strlen($input['password']) < 8) {
      error_log("Password too short");
      return false;
    }

    return true;
  }
}
