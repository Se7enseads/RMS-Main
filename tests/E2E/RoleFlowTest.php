<?php

namespace Tests\E2E;

class RoleFlowTest extends BrowserTestCase
{
    public function testManagerCanCreateRoleWithPermissionsAndEditThem(): void
    {
        $roleName = 'E2E Host ' . substr(md5((string) mt_rand()), 0, 6);
        $db = $this->liveDb();
        $kitchenId = (int) $db->query("SELECT id FROM permissions WHERE name = 'kitchen.view'")->fetchColumn();
        $barId = (int) $db->query("SELECT id FROM permissions WHERE name = 'bar.view'")->fetchColumn();

        try {
            $this->loginWithPassword('MGR001', 'manager123');

            // create a role with a permission selected
            $this->page->goto($this->baseUrl() . '/admin/roles');
            $this->page->locator('a[href="/admin/roles/create"]')->click();
            $this->page->locator('input[name="name"]')->fill($roleName);
            $this->page->locator('input[name="permissions[]"][value="' . $kitchenId . '"]')->check();
            $this->page->locator('form[action="/admin/roles/create"] button[type="submit"]')->click();
            $this->expect($this->page)->toHaveURL($this->baseUrl() . '/admin/roles');
            $this->expect($this->page->locator('.tabulator'))->toContainText($roleName);

            // edit page shows the selected permission checked
            $this->page->locator('.tabulator-row')->filter(['hasText' => $roleName])
                ->locator('a[href^="/admin/roles/edit/"]')->click();
            $this->expect($this->page->locator('input[name="permissions[]"][value="' . $kitchenId . '"]'))->toBeChecked();
            $this->expect($this->page->locator('input[name="permissions[]"][value="' . $barId . '"]'))->not()->toBeChecked();

            // add bar.view and save
            $this->page->locator('input[name="permissions[]"][value="' . $barId . '"]')->check();
            $this->page->locator('form[action^="/admin/roles/edit/"] button[type="submit"]')->click();
            $this->expect($this->page)->toHaveURL($this->baseUrl() . '/admin/roles');

            $granted = $db->query("SELECT COUNT(*) FROM role_permissions rp JOIN roles r ON r.id = rp.role_id WHERE r.name = '" . $roleName . "'")->fetchColumn();
            $this->assertSame(2, (int) $granted);
        } finally {
            $stmt = $db->prepare('DELETE FROM role_permissions WHERE role_id = (SELECT id FROM roles WHERE name = ?)');
            $stmt->execute([$roleName]);
            $stmt = $db->prepare('DELETE FROM roles WHERE name = ?');
            $stmt->execute([$roleName]);
        }
    }
}