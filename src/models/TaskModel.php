<?php

namespace App\Models;

use App\Models\SqlConnect;
use \PDO;

class TaskModel extends SqlConnect
{
  public function create(array $data)
  {
    try {
      var_dump("User ID: " . $data['user_id']);
      var_dump("Flatshare ID: " . $data['flatshare_id']);

      if (!$this->isUserInFlatshare($data['user_id'], $data['flatshare_id'])) {
        throw new \Exception("User does not belong to the specified flatshare.");
      }

      $query = "INSERT INTO tasks (task_name, task_description, deadline_spot, task_priority, task_category, status, user_id, flatmate_id, flatshare_id)
                VALUES (:task_name, :task_description, :deadline_spot, :task_priority, :task_category, :status, :user_id, :flatmate_id, :flatshare_id)";

      $stmt = $this->db->prepare($query);

      // Pas besoin de htmlspecialchars ici
      $data['task_name'] = ($data['task_name']);
      $data['task_description'] = ($data['task_description']);
      $data['deadline_spot'] = ($data['deadline_spot']);
      $data['task_priority'] = ($data['task_priority']);
      $data['task_category'] = ($data['task_category']);
      $data['status'] = ($data['status']);
      $data['user_id'] = (int)$data['user_id']; // Assurez-vous que c'est un entier
      $data['flatmate_id'] = (int)$data['flatmate_id']; // Assurez-vous que c'est un entier
      $data['flatshare_id'] = (int)$data['flatshare_id']; // Assurez-vous que c'est un entier

      var_dump($data);
      var_dump($this->transformDataInDot($data));

      if ($stmt->execute($this->transformDataInDot($data))) {
        return $this->db->lastInsertId();
      } else {
        var_dump($stmt->errorInfo());
        $this->logError($stmt->errorInfo());
        return false;
      }
    } catch (\PDOException $e) {
      $this->logError("Error in createTask: " . $e->getMessage());
      return false;
    }
  }

  private function isUserInFlatshare($userId, $flatshareId)
  {
    $query = "SELECT COUNT(*) FROM flatmates WHERE user_id = :user_id AND flatshare_id = :flatshare_id";
    $stmt = $this->db->prepare($query);
    $stmt->execute([':user_id' => $userId, ':flatshare_id' => $flatshareId]);
    $result = $stmt->fetchColumn();
    
    var_dump("isUserInFlatshare - user_id: " . $userId . ", flatshare_id: " . $flatshareId . ", result: " . $result);

    return $result > 0;
  }

  private function logError($error)
  {
    error_log(print_r($error, true));
  }
}
