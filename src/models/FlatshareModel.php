<?php

namespace App\Models;

use \PDO;
use stdClass;

class FlatshareModel extends SqlConnect
{
    public function create(array $data)
    {
        try {
            $query = "INSERT INTO flatshares (name,creation_date_flatshare, address, access_code)
                      VALUES (:name, :creation_date_flatshare, :address, :access_code)";

            $stmt = $this->db->prepare($query);

            $data['name'] = htmlspecialchars($data['name']);
            $data['creation_date_flatshare'] = date('Y/m/d H:i:s');
            $data['address'] = htmlspecialchars($data['address']);
            $data['access_code'] = htmlspecialchars($data['access_code']);

            $stmt->execute([
                ':name' => ($data['name']), ':creation_date_flatshare' => $data['creation_date_flatshare'],
                ':address' => ($data['address']),
                ':access_code' => ($data['access_code'])
            ]);

            return $this->db->lastInsertId();

        } catch (\PDOException $e) {
            /* var_dump($e); */
            error_log("Error in createFlatshare: " . $e->getMessage());
            return false;
        }
    }

    public function getFlatshareByNameAndCode($name, $access_code)
    {
        try {
            $query = "SELECT * FROM flatshares WHERE name = :name AND access_code = :access_code";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':name' => $name, ':access_code' => $access_code]);

            $flatshare = $stmt->fetch(PDO::FETCH_ASSOC);
            var_dump("Flatshare found:", $flatshare);

            return $flatshare ? $flatshare : null;
        } catch (\PDOException $e) {
            var_dump($e);
            error_log("Error in getFlatshareByNameAndCode: " . $e->getMessage());
            return null;
        }
    }

    public function addFlatmate($userId, $flatshareId)
    {
        try {
            $query = "INSERT INTO flatmates (user_id, flatshare_id) VALUES (:user_id, :flatshare_id)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':user_id' => htmlspecialchars($userId),
                ':flatshare_id' => htmlspecialchars($flatshareId)
            ]);
            return true;
        } catch (\PDOException $e) {
            var_dump($e);
            return false;
        }
    }

    public function getUserById($userId) {
        try {
          $query = "SELECT * FROM users WHERE id = :id";
          $stmt = $this->db->prepare($query);
          $stmt->execute([':id' => $userId]);
    
          $user = $stmt->fetch(PDO::FETCH_ASSOC);
          var_dump("User found:", $user);
    
          return $user ? $user : null;
        } catch (\PDOException $e) {
          var_dump($e);
          error_log("Error in getUserById: " . $e->getMessage());
          return null;
        }
      }

     public function getFlatshareByUserId($userId) {
        try {
            $query = "SELECT access_code FROM flatshares 
                      JOIN flatmates ON flatshares.id = flatmates.flatshare_id 
                      WHERE flatmates.user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':user_id' => $userId]);

            return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        } catch (\PDOException $e) {
            var_dump($e);
            error_log("Error in getFlatshareByUserId: " . $e->getMessage());
            return null;
        }
     }
}
