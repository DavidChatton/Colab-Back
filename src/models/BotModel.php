<?php

namespace App\Models;

use \PDO;
use stdClass;

class BotModel extends SqlConnect {
  public function getAll() {
    $req = $this->db->prepare("SELECT * FROM bots");
    $req->execute();

    return $req->rowCount() > 0 ? $req->fetchAll(PDO::FETCH_ASSOC) : new stdClass();
  }
}