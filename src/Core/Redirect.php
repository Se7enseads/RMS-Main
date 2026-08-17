<?php

namespace App\Core;

class Redirect
{
    public static ?string $lastUrl = null;

    public static function to(string $url): void
    {
        self::$lastUrl = $url;

        if (PHP_SAPI !== 'cli') {
            header('Location: ' . $url);
        }
    }

    public static function clear(): void
    {
        self::$lastUrl = null;
    }
}