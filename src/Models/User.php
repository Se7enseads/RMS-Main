<?php

namespace App\Models;

class User
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $employeeNum,
        public readonly string  $firstName,
        public readonly ?string $middleName,
        public readonly string  $lastName,
        public readonly string  $nationalId,
        public readonly string  $pin,
        public readonly ?string $pinHash,
        public readonly ?string $passwordHash,
        public readonly ?string $phoneNumber,
        public readonly int     $roleId,
        public readonly bool    $active,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?string $roleName = null,
    )
    {
    }

    /**
     * @param array<int, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            employeeNum: $row['employee_num'],
            firstName: $row['first_name'],
            middleName: $row['middle_name'] ?? null,
            lastName: $row['last_name'],
            nationalId: $row['national_id'],
            pin: $row['pin'],
            pinHash: $row['pin_hash'] ?? null,
            passwordHash: $row['password_hash'] ?? null,
            phoneNumber: $row['phone_number'] ?? null,
            roleId: (int)$row['role_id'],
            active: (bool)$row['active'],
            createdAt: $row['created_at'] ?? null,
            updatedAt: $row['updated_at'] ?? null,
            roleName: $row['role_name'] ?? null,
        );
    }

    public function getFullName(): string
    {
        return trim(implode(' ', array_filter([
            $this->firstName,
            $this->middleName,
            $this->lastName,
        ])));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_num' => $this->employeeNum,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'national_id' => $this->nationalId,
            'pin' => $this->pin,
            'pin_hash' => $this->pinHash,
            'password_hash' => $this->passwordHash,
            'phone_number' => $this->phoneNumber,
            'role_id' => $this->roleId,
            'active' => $this->active,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'role_name' => $this->roleName,
        ];
    }
}
