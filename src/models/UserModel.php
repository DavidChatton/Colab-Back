<?php

namespace App\Models;

use \PDO;
use stdClass;

class UserModel extends SqlConnect {
    public function createUser(array $data) {
        try {
            $query = "
                INSERT INTO users (firstname, lastname, email, password, created_at)
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

    public function updateFlatshareId(int $userId, int $flatshareId) {
        try {
            $query = "UPDATE users SET flatshare_id = :flatshare_id WHERE id = :id";
            $stmt = $this->db->prepare($query);

            $stmt->execute([
                ":flatshare_id" => $flatshareId,
                ":id" => $userId
            ]);
        } catch (\PDOException $e) {
            error_log("Error in updateFlatshareId: " . $e->getMessage());
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
}