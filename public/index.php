<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Kernel;
use Symfony\Component\HttpFoundation\Request;

$response = Kernel::handle(Request::createFromGlobals());
$response->send();