<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    private static bool $provisioned = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$provisioned) {
            DatabaseSetup::provision();
            self::$provisioned = true;
        }
    }

    protected function setUp(): void
    {
        DatabaseSetup::reset();
    }
}