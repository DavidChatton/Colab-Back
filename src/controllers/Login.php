<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\UserModel;

class Login extends Controller {
  protected object $user;

  public function __construct($param) {
    $this->user = new UserModel();
    parent::__construct($param);
  }

  public function postLogin() {
    error_log("Starting postLogin");

    // Valider les entrées du formulaire
    if (!$this->validateInput($this->body)) {
      // Retourner un code de réponse HTTP 400 pour une demande incorrecte
      header('HTTP/1.1 400 Bad Request');
      error_log("Invalid input");
      return [
        'message' => 'Invalid input'
      ];
    }

    // Récupérer l'utilisateur par e-mail
    $user = $this->user->getUserByEmail($this->body['email']);

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($user && $this->verifyPassword($this->body['password'], $user['password'])) {
      // Si c'est le cas cela démarre une session en stockant l'ID utilisateur dans la session
      session_start();
      $_SESSION['user_id'] = $user['id'];
      // Retourner un code de réponse HTTP 200 pour une connexion réussie
      header('HTTP/1.1 200 OK');
      error_log("Login successful for user ID: " . $user['id']);
      return [
        'message' => 'Login successful',
        'user_id' => $user['id'],
        'session_id' => session_id()
      ];
    }

    // Retourner un code de réponse HTTP 401 pour des informations d'identification incorrectes
    header('HTTP/1.1 401 Unauthorized');
    error_log("Invalid email or password");
    return [
      'message' => 'email invalide ou mot de passe'
    ];
  }

  private function validateInput($input) {
    // Vérifier si toutes les clés nécessaires sont présentes dans les données d'entrée
    if (!isset($input['email'], $input['password'])) {
      error_log("Missing required fields");
      return false;
    }

    // Valider le format de l'adresse e-mail
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
      error_log("Invalid email format: " . $input['email']);
      return false;
    }

    return true;
  }

  // Méthode pour vérifier le mot de passe
  private function verifyPassword($inputPassword, $hashedPassword) {
    return password_verify($inputPassword, $hashedPassword);
  }

  public function checkSession() {
    session_id($this->body['session_id']);
    session_start();

    if (isset($_SESSION['user_id'])) {
      header('HTTP/1.1 200 OK');
      return ['message' => 'Session valid'];
    } else {
      header('HTTP/1.1 401 Unauthorized');
      return ['message' => 'Invalid session'];
  }
    }
}
