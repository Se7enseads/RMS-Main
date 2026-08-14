<?php

namespace App\Controllers;

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
            header('Location: /');
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
            if ($result['user']->roleName === 'WAITER') {
                header('Location: /kiosk');
            } else {
                header('Location: /');
            }
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
        header('Location: /login');
    }
}
