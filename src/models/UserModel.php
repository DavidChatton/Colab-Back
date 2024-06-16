<?php

namespace App\models;

use \PDO;
use stdClass;
class UserModel extends SqlConnect {
    public function createUser(array $data) {
        try {
            $query = "INSERT INTO users (firstname, lastname, email, password, created_at)
                VALUES (:firstname, :lastname, :email, :password, :created_at)
            ";

            $stmt = $this->db->prepare($query);

            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            date_default_timezone_set('Europe/Paris'); // Définir le fuseau horaire à Paris
            $data['created_at'] = date('Y/m/d H:i:s');

            $stmt->execute([
                ':firstname' => htmlspecialchars($data['firstname']),
                ':lastname' => htmlspecialchars($data['lastname']),
                ':email' => htmlspecialchars($data['email']),
                ':password' => $data['password'],
                ':created_at' => $data['created_at']
            ]);
            
            return true;
        } catch (\PDOException $e) {
            error_log("Error in createUser: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([":id" => $id]);
        } catch (\PDOException $e) {
            error_log("Error in delete: " . $e->getMessage());
        }
    }

    public function get(int $id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute([":id" => $id]);

            return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : new stdClass();
        } catch (\PDOException $e) {
            error_log("Error in get: " . $e->getMessage());
            return new stdClass();
        }
    }

    public function getLast() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users ORDER BY id DESC LIMIT 1");
            $stmt->execute();

            return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : new stdClass();
        } catch (\PDOException $e) {
            error_log("Error in getLast: " . $e->getMessage());
            return new stdClass();
        }
    }

    public function getLastInsertedId() {
        try {
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Error in getLastInsertedId: " . $e->getMessage());
            return null;
        }
    }

    // Méthode pour récupérer l'utilisateur par e-mail
    public function getUserByEmail($email) {
      try {
          $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
          $stmt->execute([':email' => $email]);

          return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
      } catch (\PDOException $e) {
          error_log("Error in getUserByEmail: " . $e->getMessage());
          return null;
      }
    }

    public function getUserById($id) {
      try {
          $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
          $stmt->execute([':id' => $id]);
          return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
          var_dump($stmt);
      } catch (\PDOException $e) {
          var_dump($e);
          error_log("Error in getUserById: " . $e->getMessage());
          return null;
      }
  }

  public function putUser($id, $data) {
    try {
        if (!isset($data['lastname'], $data['email'])) {
            throw new \Exception('Missing required fields');
        }

        $query = "UPDATE users SET 
                 lastname = :lastname, 
                 email = :email 
                 WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->execute([
            ':lastname' => htmlspecialchars($data['lastname']),
            ':email' => htmlspecialchars($data['email']),
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    } catch (\PDOException $e) {
        error_log("Erreur dans la méthode putUser: " . $e->getMessage());
        return false;
    } catch (\Exception $e) {
        error_log("Validation error in putUser: " . $e->getMessage());
        return false;
    }
  }

  public function getUsersByFlatshare($flatshareId) {
    try {
        $query = "SELECT * FROM flatmates WHERE flatshare_id = :flatshare_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':flatshare_id' => $flatshareId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("Error in getUsersByFlatshare: " . $e->getMessage());
        return [];
    }
}
}
