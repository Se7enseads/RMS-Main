-- RBAC seed: permissions and role assignments
-- Run: mysql -uuser -ppassword rms < sql/seed_rbac.sql

INSERT IGNORE INTO permissions (name) VALUES
    ('dashboard.view'),
    ('kitchen.view'),
    ('users.view'),
    ('users.create'),
    ('users.update'),
    ('users.deactivate'),
    ('roles.view'),
    ('roles.create'),
    ('menu.view'),
    ('menu.create');

-- MANAGER role id = 1: grant everything
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

-- WAITER role id = 2: no admin permissions (kiosk is auth-only)
-- (deliberately empty)

-- HEAD CHEF role: kitchen display only
INSERT IGNORE INTO roles (name) VALUES ('HEAD CHEF');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.name = 'HEAD CHEF' AND p.name IN ('kitchen.view');
