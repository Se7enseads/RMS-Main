<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_NONE) return;

        session_set_cookie_params([
            'lifetime' => 1800,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();

        if (session_status() === PHP_SESSION_ACTIVE && !isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function csrfToken(): string
    {
        self::start();
        return $_SESSION['_csrf_token'] ?? ($_SESSION['_csrf_token'] = bin2hex(random_bytes(32)));
    }

    public static function validateCsrfToken(?string $token): bool
    {
        $storedToken = self::get('_csrf_token');
        if (!$storedToken || !$token) {
            return false;
        }
        return hash_equals($storedToken, $token);
    }

    public static function get(string $key): mixed
    {
        self::start();
        return $_SESSION[$key] ?? null;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        session_destroy();
        $_SESSION = [];
    }
}
