<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Tests run against a dedicated database, never the live one.
putenv('RMS_DB_HOST=127.0.0.1');
putenv('RMS_DB_PORT=3306');
putenv('RMS_DB_NAME=rms_test');
putenv('RMS_DB_USER=user');
putenv('RMS_DB_PASS=password');