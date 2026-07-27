<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class UserService
{

  private PDO $db;
  public function __construct()
  {
    $this->db = Database::getConnection();
  }

  public function getAllActiveUsers(): array
  {
    $sql = "
            SELECT 
                users.*, 
                roles.name AS role_name
            FROM users 
            LEFT JOIN roles ON users.role_id = roles.id
            WHERE users.active = 1
        ";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
