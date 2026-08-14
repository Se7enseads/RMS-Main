<?php

namespace App\Controllers;

use App\Services\RoleService;
use App\Core\View;

class RoleController
{
    private RoleService $roleService;

    public function __construct()
    {
        $this->roleService = new RoleService();
    }

    public function index(): void
    {
        $roles = $this->roleService->getAllRoles();
        View::render('roles/index', ['roles' => $roles]);
    }

    public function create(): void
    {
        View::render('roles/create');
    }

    public function save(): void
    {
        $name = $_POST['name'] ?? '';
        $result = $this->roleService->createRole($name);

        if ($result['success']) {
            header('Location: /roles');
            return;
        }

        View::render('roles/create', [
            'errors' => $result['errors'],
            'old' => ['name' => $name],
        ]);
    }
}
