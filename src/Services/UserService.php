<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

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

        if ($this->userRepository->findByNationalId($data['national_id'])) {
            return ['success' => false, 'errors' => ['national_id' => 'National ID already exists.']];
        }

        // Hash password
        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $user = $this->userRepository->insert($data);
        return ['success' => true, 'user' => $user];
    }

    /**
     * @parameter array<int, mixed> $data
     * @return array<string, mixed>
     */
    public function updateUser(int $id, array $data): array
    {
        $errors = $this->validateUserData($data, $id);

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        $existing = $this->userRepository->findByEmployeeNum($data['employee_num']);
        if ($existing && $existing->id !== $id) {
            return ['success' => false, 'errors' => ['employee_num' => 'Employee number already exists.']];
        }

        $existingNid = $this->userRepository->findByNationalId($data['national_id']);
        if ($existingNid && $existingNid->id !== $id) {
            return ['success' => false, 'errors' => ['national_id' => 'National ID already exists.']];
        }

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
     * @param int|null $excludeId Skip uniqueness checks for this user
     * @return array<string,string>
     */
    private function validateUserData(array $data, ?int $excludeId = null): array
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
            if ($excludeId === null) {
                $errors['password'] = 'Password must be at least 8 characters.';
            }
        }

        if (empty($data['role_id'])) {
            $errors['role_id'] = 'Role is required.';
        } elseif (!$this->roleRepository->findById((int)$data['role_id'])) {
            $errors['role_id'] = 'Selected role does not exist.';
        }

        return $errors;
    }
}
