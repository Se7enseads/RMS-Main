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
        $response = $this->handle('GET', '/admin/items');

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
        $this->assertSame('/admin', $response->headers->get('Location'));
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

        $response = $this->handle('GET', '/admin/items');

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testChefForbiddenFromAdmin(): void
    {
        $this->loginAs('HEAD CHEF', 3);

        $response = $this->handle('GET', '/admin/users');

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

        $response = $this->handle('GET', '/admin');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Dashboard', $response->getContent());
    }

    public function testManagerCanViewItemEditForm(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('GET', '/admin/items/edit/1');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Edit Menu Item', $response->getContent());
        $this->assertStringContainsString('Chicken Soup', $response->getContent());
    }

    public function testManagerCanUpdateMenuItem(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('POST', '/admin/items/edit/1', [
            'csrf_token' => $this->csrfToken(),
            'name' => 'Chicken Soup Deluxe',
            'description' => 'Updated',
            'price' => '250.00',
            'category_id' => '1',
        ]);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/admin/items', $response->headers->get('Location'));

        $response = $this->handle('GET', '/admin/items');
        $this->assertStringContainsString('Chicken Soup Deluxe', $response->getContent());
    }

    public function testManagerCanDeactivateMenuItem(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('POST', '/admin/items/deactivate/1', [
            'csrf_token' => $this->csrfToken(),
        ]);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/admin/items', $response->headers->get('Location'));

        // deactivated items are hidden from the active list
        $response = $this->handle('GET', '/admin/items');
        $this->assertStringNotContainsString('Chicken Soup', $response->getContent());
    }

    public function testWaiterForbiddenFromItemManagement(): void
    {
        $this->loginAs('WAITER', 2);

        $response = $this->handle('GET', '/admin/items/edit/1');
        $this->assertSame(403, $response->getStatusCode());

        $response = $this->handle('POST', '/admin/items/deactivate/1', [
            'csrf_token' => $this->csrfToken(),
        ]);
        $this->assertSame(403, $response->getStatusCode());
    }

    public function testManagerCanViewRoleEditForm(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('GET', '/admin/roles/edit/2');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Edit Role', $response->getContent());
        $this->assertStringContainsString('WAITER', $response->getContent());
    }

    public function testManagerCanUpdateRole(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('POST', '/admin/roles/edit/2', [
            'csrf_token' => $this->csrfToken(),
            'name' => 'FLOOR STAFF',
        ]);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/admin/roles', $response->headers->get('Location'));

        $response = $this->handle('GET', '/admin/roles');
        $this->assertStringContainsString('FLOOR STAFF', $response->getContent());
    }

    public function testManagerCanDeactivateRole(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('POST', '/admin/roles/deactivate/2', [
            'csrf_token' => $this->csrfToken(),
        ]);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/admin/roles', $response->headers->get('Location'));

        $response = $this->handle('GET', '/admin/roles');
        $this->assertStringContainsString('Inactive', $response->getContent());
    }

    public function testWaiterForbiddenFromRoleManagement(): void
    {
        $this->loginAs('WAITER', 2);

        $response = $this->handle('GET', '/admin/roles/edit/1');
        $this->assertSame(403, $response->getStatusCode());

        $response = $this->handle('POST', '/admin/roles/deactivate/1', [
            'csrf_token' => $this->csrfToken(),
        ]);
        $this->assertSame(403, $response->getStatusCode());
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

    public function testManagerCanCreateCategory(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('GET', '/admin/categories');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Category List', $response->getContent());

        $response = $this->handle('POST', '/admin/categories/create', [
            'csrf_token' => $this->csrfToken(),
            'name' => 'Grills',
            'station' => 'KITCHEN',
        ]);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/admin/categories', $response->headers->get('Location'));

        $response = $this->handle('GET', '/admin/categories');
        $this->assertStringContainsString('Grills', $response->getContent());
    }

    public function testManagerCanUpdateCategory(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('POST', '/admin/categories/edit/1', [
            'csrf_token' => $this->csrfToken(),
            'name' => 'Main Courses',
            'station' => 'KITCHEN',
        ]);
        $this->assertSame(302, $response->getStatusCode());

        $response = $this->handle('GET', '/admin/categories/edit/1');
        $this->assertStringContainsString('Main Courses', $response->getContent());
    }

    public function testManagerCanCreateIngredientAndAddStock(): void
    {
        $this->loginAs('MANAGER');

        $response = $this->handle('GET', '/store');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Store Dashboard', $response->getContent());

        $response = $this->handle('GET', '/store/inventory');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Store Inventory', $response->getContent());

        // water: sold by the bottle, received in cases of 12
        $response = $this->handle('POST', '/store/inventory/create', [
            'csrf_token' => $this->csrfToken(),
            'name' => 'Water 500ml',
            'base_unit' => 'pcs',
            'receive_unit' => 'case',
            'units_per_container' => '12',
            'reorder_level' => '24',
        ]);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/store/inventory', $response->headers->get('Location'));

        // 2 cases @ 600 KES per case -> 24 bottles, 50 KES per bottle
        $response = $this->handle('POST', '/store/inventory/stock/1', [
            'csrf_token' => $this->csrfToken(),
            'quantity' => '2',
            'unit' => 'case',
            'unit_cost' => '600',
        ]);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/store/inventory', $response->headers->get('Location'));

        $row = Database::getConnection()
            ->query("SELECT stock, cost_per_unit FROM inventory WHERE id = 1")
            ->fetch();
        $this->assertSame(24.0, (float) $row['stock']);
        $this->assertSame(50.0, (float) $row['cost_per_unit']);

        // 1 more case @ 720 -> weighted average (24*50 + 12*60) / 36 = 53.3333
        $this->handle('POST', '/store/inventory/stock/1', [
            'csrf_token' => $this->csrfToken(),
            'quantity' => '1',
            'unit' => 'case',
            'unit_cost' => '720',
        ]);
        $row = Database::getConnection()
            ->query("SELECT stock, cost_per_unit FROM inventory WHERE id = 1")
            ->fetch();
        $this->assertSame(36.0, (float) $row['stock']);
        $this->assertEqualsWithDelta(53.3333, (float) $row['cost_per_unit'], 0.001);

        // movements were recorded
        $count = (int) Database::getConnection()
            ->query("SELECT COUNT(*) FROM inventory_movements WHERE inventory_id = 1 AND movement_type = 'IN'")
            ->fetchColumn();
        $this->assertSame(2, $count);

        // stock page shows current state
        $response = $this->handle('GET', '/store/inventory/stock/1');
        $this->assertStringContainsString('Water 500ml', $response->getContent());
        $this->assertStringContainsString('36.000', $response->getContent());
    }

    public function testAddStockRejectsWrongUnit(): void
    {
        $this->loginAs('MANAGER');

        $this->handle('POST', '/store/inventory/create', [
            'csrf_token' => $this->csrfToken(),
            'name' => 'Tomatoes',
            'base_unit' => 'pcs',
            'receive_unit' => 'case',
            'units_per_container' => '24',
        ]);

        $response = $this->handle('POST', '/store/inventory/stock/1', [
            'csrf_token' => $this->csrfToken(),
            'quantity' => '1',
            'unit' => 'pcs',
            'unit_cost' => '10',
        ]);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Stock must be received in case', $response->getContent());
    }

    public function testManagerCanCreateItemWithRecipeAndSeeVat(): void
    {
        $this->loginAs('MANAGER');

        // beef: 1 kg received in grams
        $this->handle('POST', '/store/inventory/create', [
            'csrf_token' => $this->csrfToken(),
            'name' => 'Beef Mince',
            'base_unit' => 'g',
            'receive_unit' => 'g',
        ]);
        $this->handle('POST', '/store/inventory/stock/1', [
            'csrf_token' => $this->csrfToken(),
            'quantity' => '5000',
            'unit' => 'g',
            'unit_cost' => '0.5',
        ]);

        $response = $this->handle('POST', '/admin/items/create', [
            'csrf_token' => $this->csrfToken(),
            'name' => 'Burger',
            'description' => 'Beef burger',
            'price' => '580.00',
            'category_id' => '1',
            'ingredient_id' => ['1'],
            'quantity' => ['1' => '100'],
        ]);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/admin/items', $response->headers->get('Location'));

        $row = Database::getConnection()
            ->query("SELECT menu_item_id, inventory_id, quantity, unit FROM menu_item_ingredients WHERE menu_item_id = (SELECT MAX(id) FROM menu_items)")
            ->fetch();
        $this->assertSame(1, (int) $row['inventory_id']);
        $this->assertSame(100.0, (float) $row['quantity']);
        $this->assertSame('g', $row['unit']);

        // cost = 100g * 0.50 = 50.00; VAT = 580 - 580/1.16 = 80.00
        $response = $this->handle('GET', '/admin/items/edit/' . (int) Database::getConnection()->query('SELECT MAX(id) FROM menu_items')->fetchColumn());
        $this->assertStringContainsString('Beef Mince', $response->getContent());
        $this->assertStringContainsString('Total ingredient cost per serving:', $response->getContent());
        $this->assertStringContainsString('50.00', $response->getContent());
        $this->assertStringContainsString('VAT (16%): KES 80.00', $response->getContent());
        $this->assertStringContainsString('Net amount: KES 500.00', $response->getContent());
    }

    public function testWaiterForbiddenFromStoreAndCategories(): void
    {
        $this->loginAs('WAITER', 2);

        $response = $this->handle('GET', '/store');
        $this->assertSame(403, $response->getStatusCode());

        $response = $this->handle('GET', '/admin/categories');
        $this->assertSame(403, $response->getStatusCode());
    }

    public function testManagerCanCreateRoleWithPermissions(): void
    {
        $this->loginAs('MANAGER');

        $db = Database::getConnection();
        $kitchenId = (int) $db->query("SELECT id FROM permissions WHERE name = 'kitchen.view'")->fetchColumn();
        $barId = (int) $db->query("SELECT id FROM permissions WHERE name = 'bar.view'")->fetchColumn();

        $response = $this->handle('POST', '/admin/roles/create', [
            'csrf_token' => $this->csrfToken(),
            'name' => 'HOST',
            'permissions' => [$kitchenId, $barId],
        ]);
        $this->assertSame(302, $response->getStatusCode());

        $roleId = (int) $db->query("SELECT id FROM roles WHERE name = 'HOST'")->fetchColumn();
        $granted = $db->query("SELECT permission_id FROM role_permissions WHERE role_id = $roleId")->fetchAll();

        $this->assertCount(2, $granted);
        $this->assertSame([$kitchenId, $barId], array_map('intval', array_column($granted, 'permission_id')));
    }

    public function testManagerCanAddAndRemoveRolePermissions(): void
    {
        $this->loginAs('MANAGER');

        $db = Database::getConnection();
        $kitchenId = (int) $db->query("SELECT id FROM permissions WHERE name = 'kitchen.view'")->fetchColumn();
        $barId = (int) $db->query("SELECT id FROM permissions WHERE name = 'bar.view'")->fetchColumn();

        $this->handle('POST', '/admin/roles/create', [
            'csrf_token' => $this->csrfToken(),
            'name' => 'HOST2',
            'permissions' => [$kitchenId],
        ]);
        $roleId = (int) $db->query("SELECT id FROM roles WHERE name = 'HOST2'")->fetchColumn();

        // add bar.view
        $this->handle('POST', "/admin/roles/edit/$roleId", [
            'csrf_token' => $this->csrfToken(),
            'name' => 'HOST2',
            'permissions' => [$kitchenId, $barId],
        ]);
        $count = (int) $db->query("SELECT COUNT(*) FROM role_permissions WHERE role_id = $roleId")->fetchColumn();
        $this->assertSame(2, $count);

        // remove kitchen.view again
        $this->handle('POST', "/admin/roles/edit/$roleId", [
            'csrf_token' => $this->csrfToken(),
            'name' => 'HOST2',
            'permissions' => [$barId],
        ]);
        $remaining = array_map('intval', array_column(
            $db->query("SELECT permission_id FROM role_permissions WHERE role_id = $roleId")->fetchAll(),
            'permission_id'
        ));
        $this->assertSame([$barId], $remaining);
    }

    public function testRoleEditFormShowsSelectedPermissions(): void
    {
        $this->loginAs('MANAGER');

        $db = Database::getConnection();
        $dashboardId = (int) $db->query("SELECT id FROM permissions WHERE name = 'dashboard.view'")->fetchColumn();

        $response = $this->handle('GET', '/admin/roles/edit/1');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('value="' . $dashboardId . '" checked', $response->getContent());
$this->assertStringContainsString('Permissions', $response->getContent());
    }
}
