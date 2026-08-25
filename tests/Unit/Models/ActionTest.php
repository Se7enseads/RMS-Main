<?php

namespace Tests\Unit\Models;

use App\Models\Action;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    public function testFromRowMapsAllFields(): void
    {
        $action = Action::fromRow([
            'id' => 5,
            'user_id' => 2,
            'action' => 'Order placed: ORD-20260819-ABCD',
            'method' => 'POST',
            'url' => '/kiosk/order',
            'ip_address' => '127.0.0.1',
            'created_at' => '2026-08-19 10:00:00',
            'user_name' => 'Brian Otieno',
        ]);

        $this->assertSame(5, $action->id);
        $this->assertSame(2, $action->userId);
        $this->assertSame('Order placed: ORD-20260819-ABCD', $action->what);
        $this->assertSame('POST', $action->method);
        $this->assertSame('/kiosk/order', $action->url);
        $this->assertSame('127.0.0.1', $action->ipAddress);
        $this->assertSame('2026-08-19 10:00:00', $action->createdAt);
        $this->assertSame('Brian Otieno', $action->getFullName());
    }

    public function testFromRowWithoutUserNameFallsBackToUserId(): void
    {
        $action = Action::fromRow([
            'id' => 1,
            'user_id' => 7,
            'action' => 'Staff logged in',
            'method' => 'POST',
            'url' => '/login',
        ]);

        $this->assertSame('7', $action->getFullName());
    }

    public function testFromRequestCapturesHttpContext(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/admin/users/create';
        $_SERVER['REMOTE_ADDR'] = '192.168.1.10';

        $action = Action::fromRequest('User created: WTR002');

        $this->assertSame('POST', $action->method);
        $this->assertSame('/admin/users/create', $action->url);
        $this->assertSame('192.168.1.10', $action->ipAddress);
        $this->assertSame('User created: WTR002', $action->what);
        $this->assertSame(0, $action->id);
        $this->assertNull($action->createdAt);
    }
}