<?php

namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * Generates employee numbers in the format {2 letters}{3+ digits},
 * e.g. MR001 (MANAGER), WT002 (WAITER).
 *
 * The next number for a role is computed as MAX(existing number for that
 * role's prefix) + 1, so numbers follow the last added user of that role.
 */
class EmployeeNumberGenerator
{
    private const ROLE_PREFIXES = [
        'MANAGER' => 'MR',
        'WAITER' => 'WT',
        'HEAD CHEF' => 'HC',
        'BARTENDER' => 'BR',
        'CASHIER' => 'CS',
    ];

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function nextForRole(string $roleName): string
    {
        $prefix = $this->prefixForRole($roleName);

        $stmt = $this->db->prepare("
            SELECT MAX(CAST(SUBSTRING(employee_num, 3) AS UNSIGNED))
            FROM staff
            WHERE SUBSTRING(employee_num, 1, 2) = :prefix
              AND employee_num REGEXP '^[A-Z]{2}[0-9]+$'
        ");
        $stmt->execute(['prefix' => $prefix]);
        $max = (int) $stmt->fetchColumn();

        return $prefix . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    private function prefixForRole(string $roleName): string
    {
        $name = strtoupper(trim($roleName));
        if (isset(self::ROLE_PREFIXES[$name])) {
            return self::ROLE_PREFIXES[$name];
        }

        return substr($name, 0, 2);
    }
}