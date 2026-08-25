<?php

namespace App\Controllers;

use App\Core\View;
use App\Repositories\AuditLogRepository;

class AuditLogController
{
    private AuditLogRepository $logRepository;

    public function __construct()
    {
        $this->logRepository = new AuditLogRepository();
    }

    public function index(): void
    {
        View::render('admin/logs', [
            'logs' => $this->logRepository->findAll(),
        ]);
    }
}
