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

    public function countActive(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM staff WHERE active = 1");
        return (int) $stmt->fetchColumn();
    }

    public function findAllActive(): array
    {
        $sql = "
            SELECT staff.*, roles.name AS role_name
            FROM staff
            LEFT JOIN roles ON staff.role_id = roles.id
            WHERE staff.active = 1
        ";
        $stmt = $this->db->query($sql);

        return array_map([User::class, 'fromRow'], $stmt->fetchAll());
    }

    public function findById(int $id): ?User
    {
        $sql = "
            SELECT staff.*, roles.name AS role_name
            FROM staff
            LEFT JOIN roles ON staff.role_id = roles.id
            WHERE staff.id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? User::fromRow($row) : null;
    }

    public function findByPin(string $pin): ?User
    {
        $sql = "
            SELECT staff.*, roles.name AS role_name
            FROM staff
            LEFT JOIN roles ON staff.role_id = roles.id
            WHERE staff.pin = :pin
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pin' => $pin]);
        $row = $stmt->fetch();

        return $row ? User::fromRow($row) : null;
    }

    public function findByEmployeeNum(string $employeeNum): ?User
    {
        $sql = "
            SELECT staff.*, roles.name AS role_name
            FROM staff
            LEFT JOIN roles ON staff.role_id = roles.id
            WHERE staff.employee_num = :employee_num
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['employee_num' => $employeeNum]);
        $row = $stmt->fetch();

        return $row ? User::fromRow($row) : null;
    }

    public function findByNationalId(string $nationalId): ?User
    {
        $sql = "
            SELECT staff.*, roles.name AS role_name
            FROM staff
            LEFT JOIN roles ON staff.role_id = roles.id
            WHERE staff.national_id = :national_id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['national_id' => $nationalId]);
        $row = $stmt->fetch();

        return $row ? User::fromRow($row) : null;
    }
    /**
     * @param array<int,mixed> $data
     */
    public function insert(array $data): User
    {
        $sql = "INSERT INTO staff (employee_num, first_name, middle_name, last_name, national_id, pin, pin_hash, password_hash, phone_number, role_id, active)
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

    /**
     * @param array<int,mixed> $data
     */
    public function update(int $id, array $data): ?User
    {
        $sql = "UPDATE staff
                SET employee_num = :employee_num,
                    first_name = :first_name,
                    middle_name = :middle_name,
                    last_name = :last_name,
                    national_id = :national_id,
                    pin = :pin,
                    phone_number = :phone_number,
                    role_id = :role_id,
                    password_hash = COALESCE(:password_hash, password_hash)
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'employee_num' => $data['employee_num'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'national_id' => $data['national_id'],
            'pin' => $data['pin'],
            'phone_number' => $data['phone_number'] ?? null,
            'role_id' => (int) $data['role_id'],
            'password_hash' => $data['password_hash'] ?? null,
            'id' => $id,
        ]);

        return $this->findById($id);
    }

    public function deactivate(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE staff SET active = 0 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
