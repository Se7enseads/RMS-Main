<?php

namespace Tests\Unit\Core;

use App\Core\Middleware;
use App\Core\Session;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        Session::destroy();
        $_POST = [];
    }

    protected function tearDown(): void
    {
        Session::destroy();
        $_POST = [];
    }

    public function testCsrfSkipsWhenNotRequired(): void
    {
        $this->assertTrue(Middleware::csrf([]));
        $this->assertTrue(Middleware::csrf(['_csrf' => false]));
    }

    public function testCsrfRejectsMissingToken(): void
    {
        $this->assertFalse(Middleware::csrf(['_csrf' => true]));
    }

    public function testCsrfRejectsInvalidToken(): void
    {
        $_POST['csrf_token'] = 'invalid';
        $this->assertFalse(Middleware::csrf(['_csrf' => true]));
    }

    public function testCsrfAcceptsValidToken(): void
    {
        $_POST['csrf_token'] = Session::csrfToken();
        $this->assertTrue(Middleware::csrf(['_csrf' => true]));
    }

    public function testAuthSkipsWhenNotRequired(): void
    {
        $this->assertTrue(Middleware::auth([]));
    }

    public function testAuthRejectsAnonymousUser(): void
    {
        $this->assertFalse(Middleware::auth(['_auth' => true]));
    }

    public function testAuthAcceptsLoggedInUser(): void
    {
        Session::set('user_id', 1);
        $this->assertTrue(Middleware::auth(['_auth' => true]));
    }
}