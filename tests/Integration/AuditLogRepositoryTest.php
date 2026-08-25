<?php

namespace Tests\Integration;

use App\Core\Logger;
use App\Models\Action;
use App\Repositories\AuditLogRepository;

class AuditLogRepositoryTest extends DatabaseTestCase
{
    public function testAddWritesAnEntry(): void
    {
        $action = new Action(
            method: 'POST',
            url: '/admin/roles/create',
            ipAddress: '10.0.0.5',
            what: 'Role created: Host',
        );

        Logger::add(1, $action);

        $logs = (new AuditLogRepository())->findAll();
        $this->assertCount(1, $logs);
        $this->assertSame('Role created: Host', $logs[0]->what);
        $this->assertSame('POST', $logs[0]->method);
        $this->assertSame('/admin/roles/create', $logs[0]->url);
        $this->assertSame('10.0.0.5', $logs[0]->ipAddress);
        $this->assertSame(1, $logs[0]->userId);
        $this->assertNotNull($logs[0]->createdAt);
    }

    public function testFindAllIncludesStaffNameAndOrdersNewestFirst(): void
    {
        $logger = new AuditLogRepository();

        Logger::add(2, new Action(what: 'first entry', method: 'GET', url: '/kiosk'));
        Logger::add(3, new Action(what: 'second entry', method: 'POST', url: '/kitchen/serve/9'));

        $logs = $logger->findAll();
        $this->assertCount(2, $logs);
        $this->assertSame('second entry', $logs[0]->what);
        $this->assertSame('Chef Mkuu', $logs[0]->getFullName());
        $this->assertSame('first entry', $logs[1]->what);
        $this->assertSame('Brian Otieno', $logs[1]->getFullName());
    }

    public function testFindByUserIdFiltersEntries(): void
    {
        $logger = new AuditLogRepository();

        Logger::add(2, new Action(what: 'waiter action', method: 'GET', url: '/kiosk'));
        Logger::add(4, new Action(what: 'bartender action', method: 'POST', url: '/bar/order'));

        $logs = $logger->findByUserId(4);
        $this->assertCount(1, $logs);
        $this->assertSame('bartender action', $logs[0]->what);
        $this->assertSame(4, $logs[0]->userId);
    }
}
