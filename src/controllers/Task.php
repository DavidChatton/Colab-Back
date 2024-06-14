<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\TaskModel;

class Task extends Controller {
  protected object $taskModel;

  public function __construct($param)
  {
    $this->taskModel = new TaskModel();
    parent::__construct($param);
  }

  public function postTask() {
    // Valider les entrées du formulaire
    if (!$this->validateInput($this->body)) {
      header('HTTP/1.1 400 Bad Request');
      return ['message' => 'Invalid input'];
    }
    // Créer une tâche en utilisant les données du formulaire
    try {
        // Utilisation de l'objet TaskModel pour créer une nouvelle tâche
        $taskId = $this->taskModel->create($this->body);
        header('HTTP/1.1 201 Created');
        return [
          'message' => 'Task created successfully',
          'task_id' => $taskId
        ];
      } catch (\Exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        return ['message' => 'Failed to create task', 'error' => $e->getMessage()];
      }
    }

    private function validateInput($input) {
        // Validation des données d'entrée
        $requiredFields = ['task_name', 'task_description', 'deadline_spot', 'task_priority', 'task_category', 'status', 'user_id', 'flatshare_id'];
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                var_dump("Validation failed for field: $field - Value: " . (isset($input[$field]) ? $input[$field] : 'null'));
                return false;
            }
        }
         // Valider la date de l'échéance
        if (!strtotime($input['deadline_spot'])) {
            var_dump("Validation failed for deadline_spot: " . $input['deadline_spot']);
            exit;
        }
  
      return true;
    }
}