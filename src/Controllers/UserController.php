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

    public function edit(int $id): void
    {
        $user = $this->userService->getUserById($id);
        if (!$user) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        $roleService = new RoleService();
        $roles = $roleService->getAllRoles();
        View::render('users/edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(int $id): void
    {
        $data = $_POST;
        $result = $this->userService->updateUser($id, $data);

        if ($result['success']) {
            header('Location: /users');
            return;
        }

        $user = $this->userService->getUserById($id);
        $roleService = new RoleService();
        $roles = $roleService->getAllRoles();
        View::render('users/edit', [
            'user' => $user,
            'roles' => $roles,
            'errors' => $result['errors'],
            'old' => $data,
        ]);
    }

    public function deactivate(int $id): void
    {
        $this->userService->deactivateUser($id);
        header('Location: /users');
    }
}
