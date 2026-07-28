<?php

namespace App\Controllers;

use App\Core\View;

class IndexController
{
    public function index(): void
    {
        View::render('index/index');
    }
}
