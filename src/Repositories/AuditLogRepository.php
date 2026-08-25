<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Action;
use PDO;

class AuditLogRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * @return array<int, Action>
     */
    public function findAll(int $limit = 1000): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, CONCAT(s.first_name, ' ', s.last_name) AS user_name
             FROM audit_logs a
             JOIN staff s ON s.id = a.user_id
             ORDER BY a.id DESC
             LIMIT :limit"
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map([Action::class, 'fromRow'], $stmt->fetchAll());
    }

    /**
     * @return array<int, Action>
     */
    public function findByUserId(int $userId, int $limit = 500): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, CONCAT(s.first_name, ' ', s.last_name) AS user_name
             FROM audit_logs a
             JOIN staff s ON s.id = a.user_id
             WHERE a.user_id = :user_id
             ORDER BY a.id DESC
             LIMIT :limit"
        );
        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map([Action::class, 'fromRow'], $stmt->fetchAll());
    }
}
