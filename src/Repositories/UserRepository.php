<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAllActive(): array
    {
        $sql = "
            SELECT users.*, roles.name AS role_name
            FROM users
            LEFT JOIN roles ON users.role_id = roles.id
            WHERE users.active = 1
        ";
        $stmt = $this->db->query($sql);

        return array_map([User::class, 'fromRow'], $stmt->fetchAll());
    }

    public function findById(int $id): ?User
    {
        $sql = "
            SELECT users.*, roles.name AS role_name
            FROM users
            LEFT JOIN roles ON users.role_id = roles.id
            WHERE users.id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? User::fromRow($row) : null;
    }

    public function findByEmployeeNum(string $employeeNum): ?User
    {
        $sql = "
            SELECT users.*, roles.name AS role_name
            FROM users
            LEFT JOIN roles ON users.role_id = roles.id
            WHERE users.employee_num = :employee_num
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['employee_num' => $employeeNum]);
        $row = $stmt->fetch();

        return $row ? User::fromRow($row) : null;
    }
    /**
     * @param array<int,mixed> $data
     */
    public function insert(array $data): User
    {
        $sql = "INSERT INTO users (employee_num, first_name, middle_name, last_name, national_id, pin, pin_hash, password_hash, phone_number, role_id, active)
                VALUES (:employee_num, :first_name, :middle_name, :last_name, :national_id, :pin, :pin_hash, :password_hash, :phone_number, :role_id, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'employee_num' => $data['employee_num'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'national_id' => $data['national_id'],
            'pin' => $data['pin'],
            'pin_hash' => $data['pin_hash'] ?? null,
            'password_hash' => $data['password_hash'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'role_id' => (int) $data['role_id'],
        ]);

        return $this->findById((int) $this->db->lastInsertId());
    }
}
