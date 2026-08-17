<?php

namespace Tests\Functional;

use App\Core\Database;
use App\Core\Kernel;
use App\Core\Redirect;
use App\Core\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Integration\DatabaseTestCase;

class KernelTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Session::destroy();
        Redirect::$lastUrl = null;
    }

    protected function tearDown(): void
    {
        Session::destroy();
        Redirect::$lastUrl = null;
    }

    private function handle(string $method, string $path, array $body = []): Response
    {
        return Kernel::handle(Request::create($path, $method, $body));
    }

    private function loginAs(string $roleName, int $userId = 1): void
    {
        Session::set('user_id', $userId);
        Session::set('user_name', 'Test User');
        Session::set('role_name', $roleName);
    }

    private function csrfToken(): string
    {
        return Session::csrfToken();
    }

    public function testLoginPageRenders(): void
    {
        $response = $this->handle('GET', '/login');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Login', $response->getContent());
    }

    public function testAnonymousAdminRouteRedirectsToLogin(): void
    {
        $response = $this->handle('GET', '/items');

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/login', $response->headers->get('Location'));
    }

    public function testManagerLoginRedirectsToDashboard(): void
    {
        $response = $this->handle('POST', '/login', [
            'login_type' => 'password',
            'employee_num' => 'MGR001',
            'password' => 'manager123',
        ]);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/', $response->headers->get('Location'));
        $this->assertSame('MANAGER', Session::get('role_name'));
    }

    public function testChefPinLoginRedirectsToKitchen(): void
    {
        $response = $this->handle('POST', '/login', [
            'login_type' => 'pin',
            'pin' => '5678',
        ]);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/kitchen', $response->headers->get('Location'));
    }

    public function testWaiterPinLoginRedirectsToKiosk(): void
    {
        $response = $this->handle('POST', '/login', [
            'login_type' => 'pin',
            'pin' => '1234',
        ]);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/kiosk', $response->headers->get('Location'));
    }

    public function testLoginRejectsBadCredentials(): void
    {
        $response = $this->handle('POST', '/login', [
            'login_type' => 'password',
            'employee_num' => 'MGR001',
            'password' => 'wrong-password',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Invalid credentials', $response->getContent());
        $this->assertNull(Session::get('user_id'));
    }

    public function testAuthenticatedLoginPageRedirectsByRole(): void
    {
        $this->loginAs('HEAD CHEF');

        $response = $this->handle('GET', '/login');

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/kitchen', $response->headers->get('Location'));
    }

    public function testCsrfMissingOnPostReturns400(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('POST', '/logout');

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testCsrfInvalidOnPostReturns400(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('POST', '/logout', ['csrf_token' => 'not-the-token']);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testLogoutWithValidCsrfRedirectsToLogin(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('POST', '/logout', ['csrf_token' => $this->csrfToken()]);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/login', $response->headers->get('Location'));
        $this->assertFalse(Session::has('user_id'));
    }

    public function testGetLogoutReturns405(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('GET', '/logout');

        $this->assertSame(405, $response->getStatusCode());
    }

    public function testUnknownRouteReturns404(): void
    {
        $response = $this->handle('GET', '/does-not-exist');

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testWaiterForbiddenFromAdmin(): void
    {
        $this->loginAs('WAITER', 2);

        $response = $this->handle('GET', '/items');

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testChefForbiddenFromAdmin(): void
    {
        $this->loginAs('HEAD CHEF', 3);

        $response = $this->handle('GET', '/users');

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testWaiterForbiddenFromKitchen(): void
    {
        $this->loginAs('WAITER', 2);

        $response = $this->handle('GET', '/kitchen');

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testWaiterForbiddenFromBar(): void
    {
        $this->loginAs('WAITER', 2);

        $response = $this->handle('GET', '/bar');

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testHeadChefForbiddenFromBar(): void
    {
        $this->loginAs('HEAD CHEF', 3);

        $response = $this->handle('GET', '/bar');

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testBartenderPinLoginRedirectsToBar(): void
    {
        $response = $this->handle('POST', '/login', [
            'login_type' => 'pin',
            'pin' => '9012',
        ]);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/bar', $response->headers->get('Location'));
    }

    public function testBartenderCanViewBar(): void
    {
        $this->loginAs('BARTENDER', 4);

        $response = $this->handle('GET', '/bar');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Bar Display', $response->getContent());
    }

    public function testManagerCanViewBar(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('GET', '/bar');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Bar Display', $response->getContent());
    }

    public function testBartenderBarOrderPageShowsOnlyDrinks(): void
    {
        $this->loginAs('BARTENDER', 4);

        $response = $this->handle('GET', '/bar/order');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Place Order', $response->getContent());
        $this->assertStringContainsString('Soda', $response->getContent());
        $this->assertStringNotContainsString('Chicken Soup', $response->getContent());
    }

    public function testBartenderCanPlaceDrinksOrderFromBar(): void
    {
        $this->loginAs('BARTENDER', 4);

        $response = $this->handle('POST', '/bar/order', [
            'csrf_token' => $this->csrfToken(),
            'order_type' => 'DINE_IN',
            'table_id' => 1,
            'items' => '[{"menu_item_id":2,"quantity":2}]',
        ]);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/bar', $response->headers->get('Location'));

        // order appears on the bar display
        $response = $this->handle('GET', '/bar');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Mark as Served', $response->getContent());
    }

    public function testBarOrderRejectsFoodItems(): void
    {
        $this->loginAs('BARTENDER', 4);

        $response = $this->handle('POST', '/bar/order', [
            'csrf_token' => $this->csrfToken(),
            'order_type' => 'DINE_IN',
            'table_id' => 1,
            'items' => '[{"menu_item_id":1,"quantity":1}]',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('not served at the bar', $response->getContent());
    }

    public function testManagerCanViewKitchen(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('GET', '/kitchen');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Kitchen Display', $response->getContent());
    }

    public function testManagerCanViewDashboard(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('GET', '/');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Dashboard', $response->getContent());
    }

    public function testKitchenOrderFlowEndToEnd(): void
    {
        $this->loginAs('MANAGER');

        // place an order via the kiosk
        $response = $this->handle('POST', '/kiosk/order', [
            'csrf_token' => $this->csrfToken(),
            'order_type' => 'DINE_IN',
            'table_id' => 1,
            'items' => '[{"menu_item_id":1,"quantity":2}]',
        ]);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/kiosk', $response->headers->get('Location'));

        $db = Database::getConnection();
        $orderId = (int) $db->query('SELECT MAX(id) FROM orders')->fetchColumn();
        $this->assertGreaterThan(0, $orderId);

        // order appears in the kitchen display
        $response = $this->handle('GET', '/kitchen');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Mark as Served', $response->getContent());

        // serve it
        $response = $this->handle('POST', "/kitchen/serve/$orderId", [
            'csrf_token' => $this->csrfToken(),
        ]);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/kitchen', $response->headers->get('Location'));

        // moved to served history
        $response = $this->handle('GET', '/kitchen');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Served Orders (1)', $response->getContent());

        // order no longer in kiosk open orders
        $response = $this->handle('GET', '/kiosk');
        $this->assertStringContainsString('No open orders', $response->getContent());
    }

    public function testBarOrderFlowEndToEnd(): void
    {
        $this->loginAs('MANAGER');

        // place a drinks-only order via the kiosk
        $response = $this->handle('POST', '/kiosk/order', [
            'csrf_token' => $this->csrfToken(),
            'order_type' => 'DINE_IN',
            'table_id' => 1,
            'items' => '[{"menu_item_id":2,"quantity":1}]',
        ]);
        $this->assertSame(302, $response->getStatusCode());

        $db = Database::getConnection();
        $orderId = (int) $db->query('SELECT MAX(id) FROM orders')->fetchColumn();

        // drinks-only order is NOT on the kitchen display
        $response = $this->handle('GET', '/kitchen');
        $this->assertStringContainsString('No orders waiting', $response->getContent());

        // but it is on the bar display
        $response = $this->handle('GET', '/bar');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Mark as Served', $response->getContent());

        // serve it from the bar
        $response = $this->handle('POST', "/bar/serve/$orderId", [
            'csrf_token' => $this->csrfToken(),
        ]);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/bar', $response->headers->get('Location'));

        $response = $this->handle('GET', '/bar');
        $this->assertStringContainsString('Served Orders (1)', $response->getContent());
    }

    public function testMixedOrderSplitsAcrossKitchenAndBar(): void
    {
        $this->loginAs('MANAGER');

        // food + drink on the same order
        $response = $this->handle('POST', '/kiosk/order', [
            'csrf_token' => $this->csrfToken(),
            'order_type' => 'DINE_IN',
            'table_id' => 1,
            'items' => '[{"menu_item_id":1,"quantity":1},{"menu_item_id":2,"quantity":1}]',
        ]);
        $this->assertSame(302, $response->getStatusCode());

        $db = Database::getConnection();
        $orderId = (int) $db->query('SELECT MAX(id) FROM orders')->fetchColumn();

        // kitchen shows only the food, bar shows only the drink
        $response = $this->handle('GET', '/kitchen');
        $this->assertStringContainsString('Chicken Soup', $response->getContent());
        $this->assertStringNotContainsString('Soda', $response->getContent());

        $response = $this->handle('GET', '/bar');
        $this->assertStringContainsString('Soda', $response->getContent());
        $this->assertStringNotContainsString('Chicken Soup', $response->getContent());

        // serving at the kitchen keeps the order open until the bar serves too
        $response = $this->handle('POST', "/kitchen/serve/$orderId", [
            'csrf_token' => $this->csrfToken(),
        ]);
        $this->assertSame(302, $response->getStatusCode());

        $response = $this->handle('GET', '/kiosk');
        $this->assertStringContainsString('PLACED', $response->getContent());

        // bar serves -> order fully served
        $response = $this->handle('POST', "/bar/serve/$orderId", [
            'csrf_token' => $this->csrfToken(),
        ]);
        $this->assertSame(302, $response->getStatusCode());

        $response = $this->handle('GET', '/kiosk');
        $this->assertStringContainsString('No open orders', $response->getContent());
    }

    public function testKioskOrderRejectsInvalidItemsJson(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('POST', '/kiosk/order', [
            'csrf_token' => $this->csrfToken(),
            'order_type' => 'TAKEAWAY',
            'table_id' => 0,
            'items' => 'not-json',
        ]);

        $this->assertSame(200, $response->getStatusCode());
    }
}