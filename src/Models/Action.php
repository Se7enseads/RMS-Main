<?php

namespace App\Models;

/**
 * A single audit log entry: what was done (what), by whom (userId),
 * and the request context (http verb, url, time, ip).
 */
class Action
{
    public function __construct(
        public readonly int     $id = 0,
        public readonly int     $userId = 0,
        public readonly string  $method = 'GET',
        public readonly string  $url = '',
        public readonly ?string $ipAddress = null,
        public readonly string  $what = '',
        public readonly ?string $createdAt = null,
        public readonly ?string $userName = null,
    )
    {
    }

    /**
     * Build a loggable action from the current request context.
     */
    public static function fromRequest(string $what): self
    {
        return new self(
            method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
            url: $_SERVER['REQUEST_URI'] ?? '/',
            ipAddress: $_SERVER['REMOTE_ADDR'] ?? null,
            what: $what,
        );
    }

    /**
     * @param array<int, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            userId: (int)$row['user_id'],
            method: $row['method'],
            url: $row['url'],
            ipAddress: $row['ip_address'] ?? null,
            what: $row['action'],
            createdAt: $row['created_at'] ?? null,
            userName: $row['user_name'] ?? null,
        );
    }

    public function getFullName(): string
    {
        return $this->userName ?? (string)$this->userId;
    }
}
