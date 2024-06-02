<?php

namespace App\Controllers;

class Controller {
  protected array $params;
  protected string $reqMethod;
  protected array $body;
  protected string $className;

  public function __construct($params) {
    $this->className = $this->getCallerClassName();
    $this->params = $params;
    $this->reqMethod = strtolower($_SERVER['REQUEST_METHOD']);
    $this->body = (array) json_decode(file_get_contents('php://input'));
    /* $this->header(); */
    $this->ifMethodExist();
  }

  protected function getCallerClassName() {
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);

    if (isset($backtrace[1]['object'])) {
      $fullClassName = get_class($backtrace[1]['object']);
      $className = basename(str_replace('\\', '/', $fullClassName));

      return $className;
    }

    return 'Unknown';
  }

  /* protected function header() {
    header('Access-Control-Allow-Origin: *');
    header('Content-type: application/json; charset=utf-8');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      header('Access-Control-Allow-Origin: *');
      header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
      header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
      header('Access-Control-Max-Age: 86400');
      http_response_code(200);
      exit;
    }
  } */

  protected function ifMethodExist() {
    $method = $this->reqMethod.''.$this->className;

    if (method_exists($this, $method)) {
      echo json_encode($this->$method());

      return;
    }

    header('HTTP/1.0 404 Not Found');
    echo json_encode([
      'code' => '404',
      'message' => 'Not Found'
    ]);

    return;
  }

  public function checkSession() {
    // Valider que l'ID de session est présent
    if (!isset($this->body['session_id'])) {
      header('HTTP/1.1 401 Unauthorized');
      return ['message' => 'Session ID Manquante'];
    }

    // Démarrer la session avec l'ID de session fourni
    session_id($this->body['session_id']);
    session_start();

    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['user_id'])) {
      header('HTTP/1.1 200 OK');
      return ['message' => 'Session valide'];
    } else {
      header('HTTP/1.1 401 Unauthorized');
      return ['message' => 'Session Invalide'];
    }
  }
}
