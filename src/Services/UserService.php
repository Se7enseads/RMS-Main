<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;

class UserService
{
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

        // Hash password
        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $user = $this->userRepository->insert($data);
        return ['success' => true, 'user' => $user];
    }

    /**
     * @param array<int,mixed> $data
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

        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        if (empty($data['role_id'])) {
            $errors['role_id'] = 'Role is required.';
        } elseif (!$this->roleRepository->findById((int) $data['role_id'])) {
            $errors['role_id'] = 'Selected role does not exist.';
        }

        return $errors;
    }
}
