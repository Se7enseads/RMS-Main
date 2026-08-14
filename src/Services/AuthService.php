<?php

namespace App\Services;

use App\Core\Session;
use App\Models\User;
use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function loginWithPassword(string $employeeNum, string $password): array
    {
        $user = $this->userRepository->findByEmployeeNum($employeeNum);

        if (!$user || !$user->active) {
            return ['success' => false, 'error' => 'Invalid credentials.'];
        }

        if (!$user->passwordHash || !password_verify($password, $user->passwordHash)) {
            return ['success' => false, 'error' => 'Invalid credentials.'];
        }

        $this->startSession($user, 'password');

        return ['success' => true, 'user' => $user];
    }

    public function loginWithPin(string $pin): array
    {
        $user = $this->userRepository->findByPin($pin);

        if (!$user || !$user->active) {
            return ['success' => false, 'error' => 'Invalid PIN.'];
        }

        $this->startSession($user, 'pin');

        return ['success' => true, 'user' => $user];
    }

    private function startSession(User $user, string $type): void
    {
        Session::set('user_id', $user->id);
        Session::set('user_name', $user->getFullName());
        Session::set('role_id', $user->roleId);
        Session::set('login_type', $type);
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public function isAuthenticated(): bool
    {
        Session::start();
        return Session::has('user_id');
    }

    public function getCurrentUser(): ?User
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return null;
        }

        return $this->userRepository->findById($userId);
    }
}
