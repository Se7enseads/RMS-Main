<?php

namespace App\Controllers;

use App\Services\RoleService;
use App\Services\UserService;
use App\Core\View;

/**
 * Fetch all users from the database, including their roles.
 *
 * @return array List of users
 */
class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function index(): void
    {
        $users = $this->userService->getAllActiveUsers();
        View::render('users/index', ['users' => $users]);
    }

    public function create(): void
    {
        $roleService = new RoleService();
        $roles = $roleService->getAllRoles();
        View::render('users/create', ['roles' => $roles]);
    }

    public function save(): void
    {
        $data = $_POST;
        $result = $this->userService->createUser($data);

        if ($result['success']) {
            header('Location: /users');
            return;
        }

        $roleService = new RoleService();
        $roles = $roleService->getAllRoles();
        View::render('users/create', [
            'roles' => $roles,
            'errors' => $result['errors'],
            'old' => $data,
        ]);
    }
}
