<?php

namespace App\Controllers;

use App\Core\View;

class KioskController
{
    public function index(): void
    {
        View::render('kiosk/index');
    }
}
