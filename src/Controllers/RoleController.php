<?php

namespace App\Controllers;

use App\Core\Redirect;
use App\Core\View;
use App\Services\RoleService;

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
            Redirect::to('/roles');
            return;
        }

        View::render('roles/create', [
            'errors' => $result['errors'],
            'old' => ['name' => $name],
        ]);
    }
}
