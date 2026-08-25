<?php

namespace App\Controllers;

use App\Core\Logger;
use App\Core\Redirect;
use App\Core\Session;
use App\Core\View;
use App\Models\Action;
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
        View::render('roles/create', [
            'permissions' => $this->roleService->getAllPermissions(),
        ]);
    }

    public function save(): void
    {
        $name = $_POST['name'] ?? '';
        $permissionIds = array_map('intval', (array) ($_POST['permissions'] ?? []));
        $result = $this->roleService->createRole($name, $permissionIds);

        if ($result['success']) {
            Logger::add((int)Session::get('user_id'), Action::fromRequest('Role created: ' . $name));
            Redirect::to('/admin/roles');
            return;
        }

        View::render('roles/create', [
            'permissions' => $this->roleService->getAllPermissions(),
            'errors' => $result['errors'],
            'old' => ['name' => $name, 'permissions' => $permissionIds],
        ]);
    }

    public function edit(int $id): void
    {
        $role = $this->roleService->getRoleById($id);
        if (!$role) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        View::render('roles/edit', [
            'role' => $role,
            'permissions' => $this->roleService->getAllPermissions(),
            'selectedIds' => $this->roleService->getPermissionIdsByRoleId($id),
        ]);
    }

    public function update(int $id): void
    {
        $name = $_POST['name'] ?? '';
        $permissionIds = array_map('intval', (array) ($_POST['permissions'] ?? []));
        $result = $this->roleService->updateRole($id, $name, $permissionIds);

        if ($result['success']) {
            Logger::add((int)Session::get('user_id'), Action::fromRequest('Role updated: ' . $name));
            Redirect::to('/admin/roles');
            return;
        }

        View::render('roles/edit', [
            'role' => $this->roleService->getRoleById($id),
            'permissions' => $this->roleService->getAllPermissions(),
            'selectedIds' => $permissionIds,
            'errors' => $result['errors'],
            'old' => ['name' => $name, 'permissions' => $permissionIds],
        ]);
    }

    public function deactivate(int $id): void
    {
        $role = $this->roleService->getRoleById($id);
        if ($role) {
            $this->roleService->setRoleActive($id, !$role->active);
            Logger::add((int)Session::get('user_id'), Action::fromRequest(
                ($role->active ? 'Role deactivated: ' : 'Role activated: ') . $role->name
            ));
        }
        Redirect::to('/admin/roles');
    }
}