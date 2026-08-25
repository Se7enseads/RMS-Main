<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

class UserService
{
    private const COMMON_PASSWORDS = [
        'password', 'password1', 'password12', 'password123', 'password1234', 'password123!',
        '123456', '1234567', '12345678', '123456789', '1234567890', '12345678910',
        'qwerty', 'qwerty123', 'qwerty123!', 'abc123', 'admin', 'admin123', 'admin123!',
        'letmein', 'welcome', 'welcome1', 'iloveyou', 'monkey', 'dragon',
        'sunshine', 'princess', 'football', 'superman', 'batman', 'trustno1',
        'passw0rd', 'p@ssw0rd', 'p@ssw0rd1234', 'changeme', 'master', 'shadow',
        '000000', '111111', '11111111', '121212', '123123', '654321', '666666', '888888',
    ];

    private UserRepository $userRepository;
    private RoleRepository $roleRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->roleRepository = new RoleRepository();
    }

    public function getAllActiveUsers(): array
    {
        return $this->userRepository->findAllActive();
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function getUserByEmployeeNum(string $employeeNum): ?User
    {
        return $this->userRepository->findByEmployeeNum($employeeNum);
    }

    /**
     * @param array<int,mixed> $data
     * @return array<string,mixed>
     */
    public function createUser(array $data): array
    {
        $errors = $this->validateUserData($data);

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check uniqueness
        if ($this->userRepository->findByEmployeeNum($data['employee_num'])) {
            return ['success' => false, 'errors' => ['employee_num' => 'Employee number already exists.']];
        }

        if ($this->userRepository->findByNationalId($data['national_id'])) {
            return ['success' => false, 'errors' => ['national_id' => 'National ID already exists.']];
        }

        // Hash password
        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $user = $this->userRepository->insert($data);
        return ['success' => true, 'user' => $user];
    }

    /**
     * @param array<int, mixed> $data
     * @return array<string, mixed>
     */
    public function updateUser(int $id, array $data): array
    {
        $existing = $this->userRepository->findById($id);
        if (!$existing) {
            return ['success' => false, 'errors' => ['user' => 'User not found.']];
        }

        // employee_num and national_id are immutable
        $data['employee_num'] = $existing->employeeNum;
        $data['national_id'] = $existing->nationalId;

        $errors = $this->validateUserData($data);

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $user = $this->userRepository->update($id, $data);
        return ['success' => true, 'user' => $user];
    }

    public function deactivateUser(int $id): array
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            return ['success' => false, 'errors' => ['user' => 'User not found.']];
        }

        $this->userRepository->deactivate($id);
        return ['success' => true, 'user' => $user];
    }

    /**
     * @parameter array<int, mixed> $data
     * @return array<string,string>
     */
    private function validateUserData(array $data): array
    {
        $errors = [];

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required.';
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required.';
        }

        if (empty($data['employee_num'])) {
            $errors['employee_num'] = 'Employee number is required.';
        }

        if (empty($data['national_id'])) {
            $errors['national_id'] = 'National ID is required.';
        }

        if (empty($data['pin'])) {
            $errors['pin'] = 'PIN is required.';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required.';
        } else {
            $roleName = null;
            if (!empty($data['role_id'])) {
                $role = $this->roleRepository->findById((int)$data['role_id']);
                if ($role) {
                    $roleName = $role->name;
                }
            }

            $passwordError = $this->validatePassword($data['password'], $roleName, [
                $data['first_name'] ?? '',
                $data['middle_name'] ?? '',
                $data['last_name'] ?? '',
            ]);
            if ($passwordError) {
                $errors['password'] = $passwordError;
            }
        }

        if (empty($data['role_id'])) {
            $errors['role_id'] = 'Role is required.';
        } elseif (!$this->roleRepository->findById((int)$data['role_id'])) {
            $errors['role_id'] = 'Selected role does not exist.';
        }

        return $errors;
    }

    /**
     * @param array<int, string> $nameParts
     */
    private function validatePassword(string $password, ?string $roleName, array $nameParts): ?string
    {
        $length = strlen($password);
        if ($length < 12) {
            return 'Password must be at least 12 characters.';
        }
        if ($length > 64) {
            return 'Password must be at most 64 characters.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return 'Password must contain at least one special character.';
        }
        if (in_array(strtolower($password), self::COMMON_PASSWORDS, true)) {
            return 'Password is too common. Choose a stronger one.';
        }

        $lower = strtolower($password);
        foreach (array_merge($nameParts, [$roleName]) as $part) {
            $part = trim((string)$part);
            if (strlen($part) >= 3 && str_contains($lower, strtolower($part))) {
                return 'Password must not contain the user\'s name or role.';
            }
        }

        return null;
    }
}