<?php

namespace App\Controllers;

use App\Core\Redirect;
use App\Core\Session;
use App\Core\View;
use App\Services\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function loginForm(): void
    {
        if ($this->authService->isAuthenticated()) {
            Redirect::to($this->homePath());
            return;
        }

        View::render('auth/login');
    }

    public function login(): void
    {
        Session::start();

        $type = $_POST['login_type'] ?? 'password';

        if ($type === 'pin') {
            $pin = $_POST['pin'] ?? '';

            if (empty($pin)) {
                View::render('auth/login', [
                    'error' => 'PIN is required.',
                    'login_type' => 'pin',
                ]);
                return;
            }

            $result = $this->authService->loginWithPin($pin);
        } else {
            $employeeNum = $_POST['employee_num'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($employeeNum) || empty($password)) {
                View::render('auth/login', [
                    'error' => 'Employee number and password are required.',
                    'login_type' => 'password',
                ]);
                return;
            }

            $result = $this->authService->loginWithPassword($employeeNum, $password);
        }

        if ($result['success']) {
            Redirect::to($this->homePath($result['user']->roleName));
            return;
        }

        View::render('auth/login', [
            'error' => $result['error'],
            'login_type' => $type,
        ]);
    }

    public function logout(): void
    {
        $this->authService->logout();
        Redirect::to('/login');
    }

    private function homePath(?string $roleName = null): string
    {
        $roleName = $roleName ?? Session::get('role_name');

        return match ($roleName) {
            'WAITER' => '/kiosk',
            'HEAD CHEF' => '/kitchen',
            'BARTENDER' => '/bar',
            default => '/admin',
        };
    }
}
