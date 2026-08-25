<?php

namespace App\Core;

use App\Models\Action;
use PDO;

/**
 * Writes audit log entries for staff actions.
 *
 * Usage: Logger::add((int) Session::get('user_id'), Action::fromRequest('Order placed'));
 */
final class Logger
{
    public static function add(int $userId, Action $action): void
    {
        $stmt = Database::getConnection()->prepare(
            'INSERT INTO audit_logs (user_id, action, method, url, ip_address)
             VALUES (:user_id, :action, :method, :url, :ip_address)'
        );

        $stmt->execute([
            'user_id' => $userId,
            'action' => $action->what,
            'method' => $action->method,
            'url' => $action->url,
            'ip_address' => $action->ipAddress,
        ]);
    }
}
