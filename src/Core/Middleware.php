<?php

namespace App\Core;

use App\Repositories\PermissionRepository;

class Middleware
{
    /**
     * Checks if the request passes CSRF validation.
     */
    public static function csrf(array $routeParams): bool
    {
        $requiresCsrf = $routeParams['_csrf'] ?? false;

        if ($requiresCsrf) {
            $submittedToken = $_POST['csrf_token'] ?? null;
            return Session::validateCsrfToken($submittedToken);
        }

        return true;
    }

    /**
     * Checks if the user passes the authentication requirement.
     */
    public static function auth(array $routeParams): bool
    {
        $requiresAuth = $routeParams['_auth'] ?? false;

        if ($requiresAuth && !Session::has('user_id')) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the user's role has the required permission.
     */
    public static function permissions(array $routeParams): bool
    {
        $requiredPermission = $routeParams['_permission'] ?? null;

        if (!$requiredPermission) {
            return true;
        }

        $roleName = Session::get('role_name');
        if (!$roleName) {
            return false;
        }

        $permissionRepo = new PermissionRepository();
        return $permissionRepo->roleHasPermissionByName($roleName, $requiredPermission);
    }
}