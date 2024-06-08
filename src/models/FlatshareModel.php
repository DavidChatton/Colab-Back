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

    public function addFlatmate($userId, $flatshareId)
    {
        try {
            $query = "INSERT INTO flatmates (user_id, flatshare_id) VALUES (:user_id, :flatshare_id)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':flatshare_id' => $flatshareId
            ]);
            return true;
        } catch (\PDOException $e) {
            error_log("Error in addFlatmate: " . $e->getMessage());
            return false;
        }
    }
}
