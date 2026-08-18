-- RBAC seed: roles, permissions and role assignments
-- Run: mysql -uuser -ppassword rms < sql/seed_rbac.sql

-- Core roles (idempotent)
INSERT IGNORE INTO roles (name) VALUES ('MANAGER'), ('WAITER');

INSERT IGNORE INTO permissions (name) VALUES
    ('dashboard.view'),
    ('kitchen.view'),
    ('bar.view'),
    ('users.view'),
    ('users.create'),
    ('users.update'),
    ('users.deactivate'),
    ('roles.view'),
    ('roles.create'),
    ('roles.update'),
    ('roles.deactivate'),
    ('menu.view'),
    ('menu.create'),
    ('menu.update'),
    ('menu.deactivate'),
    ('categories.view'),
    ('categories.create'),
    ('categories.update'),
    ('categories.deactivate'),
    ('inventory.view'),
    ('inventory.create'),
    ('inventory.update'),
    ('inventory.deactivate');

-- MANAGER role: grant everything
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.name = 'MANAGER';

-- WAITER role id = 2: no admin permissions (kiosk is auth-only)
-- (deliberately empty)

-- HEAD CHEF role: kitchen display only
INSERT IGNORE INTO roles (name) VALUES ('HEAD CHEF');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.name = 'HEAD CHEF' AND p.name IN ('kitchen.view');

-- BARTENDER role: bar display only
INSERT IGNORE INTO roles (name) VALUES ('BARTENDER');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.name = 'BARTENDER' AND p.name IN ('bar.view');
