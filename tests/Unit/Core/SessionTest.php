<?php

namespace Tests\Unit\Core;

use App\Core\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        Session::destroy();
    }

    protected function tearDown(): void
    {
        Session::destroy();
    }

    public function testCsrfTokenIsStableAndHex(): void
    {
        $first = Session::csrfToken();
        $second = Session::csrfToken();

        $this->assertSame($first, $second);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $first);
    }

    public function testValidateCsrfTokenAcceptsStoredToken(): void
    {
        $token = Session::csrfToken();
        $this->assertTrue(Session::validateCsrfToken($token));
    }

    public function testValidateCsrfTokenRejectsWrongToken(): void
    {
        Session::csrfToken();
        $this->assertFalse(Session::validateCsrfToken('wrong'));
        $this->assertFalse(Session::validateCsrfToken(null));
        $this->assertFalse(Session::validateCsrfToken(''));
    }

    public function testSetAndGet(): void
    {
        Session::set('user_id', 42);
        $this->assertSame(42, Session::get('user_id'));
        $this->assertTrue(Session::has('user_id'));
    }

    public function testGetMissingKeyReturnsNull(): void
    {
        $this->assertNull(Session::get('does_not_exist'));
        $this->assertFalse(Session::has('does_not_exist'));
    }

    public function testRemove(): void
    {
        Session::set('key', 'value');
        Session::remove('key');
        $this->assertNull(Session::get('key'));
    }

    public function testDestroyClearsEverything(): void
    {
        Session::set('user_id', 42);
        Session::destroy();
        $this->assertFalse(Session::has('user_id'));
    }
}