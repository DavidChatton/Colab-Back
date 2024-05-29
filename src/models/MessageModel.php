<?php

namespace App\Models;

use \PDO;
use stdClass;

class MessageModel extends SqlConnect {
  public function getAll() {
    $req = $this->db->prepare("SELECT * FROM messages");
    $req->execute();

    return $req->rowCount() > 0 ? $req->fetchAll(PDO::FETCH_ASSOC) : new stdClass();
  }

  public function get($id) {
    $req = $this->db->prepare("SELECT * FROM messages WHERE id=:id");
    $req->execute(["id" => $id]);

    return $req->rowCount() > 0 ? $req->fetchAll(PDO::FETCH_ASSOC) : new stdClass();
  }

  public function delete(int $id) {
    $req = $this->db->prepare("DELETE FROM messages WHERE id = :id");
    $req->execute(["id" => $id]);
  }

  public function add($data) {
    // suppresion of is_user and name
    $query = "
      INSERT INTO messages (name, message, isUser)
      VALUES (?, ?, ?)
    ";

    $req = $this->db->prepare($query);
    $req->execute([
      $data['name'],
      $data['message'],// 
      $data['isUser']
    ]);
  }

  public function getLast() {
    $req = $this->db->prepare("SELECT * FROM messages ORDER BY id DESC LIMIT 1");
    $req->execute();

    return $req->rowCount() > 0 ? $req->fetch(PDO::FETCH_ASSOC) : new stdClass();
  }
}