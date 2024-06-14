<?php

namespace App\Controllers;

use App\Controllers\Controller;

class Logout extends Controller {

  public function postLogout() {
    // Valider que l'ID de session est présent
    if (!isset($this->body['session_id'])) {
      header('HTTP/1.1 400 Bad Request');
      return ['message' => 'Session ID missing'];
    }

    // Démarrer la session avec l'ID de session fourni
    session_id($this->body['session_id']);
    session_start();

    // Détruire la session
    session_destroy();
    // Supprimer le cookie user_id
    setcookie("user_id", "", time() - 3600, "/");

    header('HTTP/1.1 200 OK');
    return ['message' => 'Logout successful'];
  }
}
