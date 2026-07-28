<?php

namespace App\Core;

use RuntimeException;

class View
{
    /**
     * Render a view file with data, wrapped in a directory layout if present.
     *
     * @param string $viewPath Relative path to the view file
     * @param array $data Associative array of data to pass to the view
     */
    public static function render(string $viewPath, array $data = []): void
    {
        if (str_contains($viewPath, '..')) {
            throw new RuntimeException("Invalid view path.");
        }

        $file = __DIR__ . '/../../src/Views/' . $viewPath . '.php';

        if (!file_exists($file)) {
            throw new RuntimeException("View file '$file' not found.");
        }

        $layout = dirname($file) . '/layout.php';
        extract($data);

        if (file_exists($layout)) {
            ob_start();
            require $file;
            $slot = ob_get_clean();
            require $layout;
        } else {
            require $file;
        }
    }
}
