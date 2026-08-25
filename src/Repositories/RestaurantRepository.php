<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class RestaurantRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getDetails(): ?array
    {
        $stmt = $this->db->query('SELECT * FROM restaurant_details ORDER BY id LIMIT 1');
        $row = $stmt->fetch();

        return $row ?: null;
    }
}