-- Staff Module
-- ---------------------
CREATE TABLE IF NOT EXISTS roles
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    active     BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS permissions
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS staff
(
    id            INT PRIMARY KEY AUTO_INCREMENT,
    employee_num  VARCHAR(20)  NOT NULL UNIQUE,
    first_name    VARCHAR(100) NOT NULL,
    middle_name   VARCHAR(100),
    last_name     VARCHAR(100) NOT NULL,
    national_id   VARCHAR(50)  NOT NULL UNIQUE,
    pin           VARCHAR(4)   NOT NULL UNIQUE,
    pin_hash      VARCHAR(255),
    password_hash VARCHAR(255),
    phone_number  VARCHAR(20),
    role_id       INT          NOT NULL,
    active        BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at    TIMESTAMP             DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP             DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id) REFERENCES roles (id),

    INDEX (first_name),
    INDEX (last_name)
);

CREATE TABLE IF NOT EXISTS role_permissions
(
    role_id       INT NOT NULL,
    permission_id INT NOT NULL,

    PRIMARY KEY (role_id, permission_id),

    FOREIGN KEY (role_id) references roles (id),
    FOREIGN KEY (permission_id) references permissions (id)
);

-- Inventory Module
-- -----------------

CREATE TABLE IF NOT EXISTS inventory
(
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    name                VARCHAR(100)                                         NOT NULL UNIQUE,
    base_unit           ENUM ('g','ml','pcs')                                NOT NULL DEFAULT 'pcs',
    receive_unit        ENUM ('g','ml','pcs','case','packet','carton','box') NOT NULL DEFAULT 'pcs',
    units_per_container DECIMAL(8, 2),
    stock               DECIMAL(12, 3)                                       NOT NULL DEFAULT 0,
    cost_per_unit       DECIMAL(12, 4)                                       NOT NULL DEFAULT 0,
    reorder_level       DECIMAL(12, 3)                                       NOT NULL DEFAULT 0,
    active              BOOLEAN                                              NOT NULL DEFAULT TRUE,
    created_at          TIMESTAMP                                                     DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP                                                     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS inventory_movements
(
    id             INT PRIMARY KEY AUTO_INCREMENT,
    inventory_id   INT                                                  NOT NULL,
    movement_type  ENUM ('IN', 'OUT', 'ADJUSTMENT')                     NOT NULL,
    quantity       DECIMAL(10, 2)                                       NOT NULL,
    reference_type ENUM ('ORDER', 'MANUAL', 'STOCK_TAKE'),
    reference_id   INT,
    performed_by   INT                                                  NOT NULL,
    unit           ENUM ('g','ml','pcs','case','packet','carton','box') NOT NULL DEFAULT 'pcs',
    unit_cost      DECIMAL(12, 4)                                       NOT NULL DEFAULT 0,
    created_at     TIMESTAMP                                                     DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (inventory_id) REFERENCES inventory (id),
    FOREIGN KEY (performed_by) REFERENCES staff (id),

    INDEX (created_at),
    INDEX (movement_type),
    INDEX (reference_type, reference_id) -- *
);

CREATE TABLE IF NOT EXISTS stock_takes
(
    id           INT PRIMARY KEY AUTO_INCREMENT,
    scope        ENUM ('ALL', 'BAR') NOT NULL DEFAULT 'ALL',
    take_date    DATE                NOT NULL,
    performed_by INT                 NOT NULL,
    notes        VARCHAR(255),
    created_at   TIMESTAMP                    DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (performed_by) REFERENCES staff (id),

    INDEX (take_date),
    INDEX (scope)
);

CREATE TABLE IF NOT EXISTS stock_take_items
(
    id             INT PRIMARY KEY AUTO_INCREMENT,
    stock_take_id  INT            NOT NULL,
    inventory_id   INT            NOT NULL,
    system_qty     DECIMAL(12, 3) NOT NULL,
    counted_qty    DECIMAL(12, 3) NOT NULL,
    variance_qty   DECIMAL(12, 3) NOT NULL,
    unit_cost      DECIMAL(12, 4) NOT NULL DEFAULT 0,
    variance_value DECIMAL(12, 2) NOT NULL DEFAULT 0,

    UNIQUE (stock_take_id, inventory_id),

    FOREIGN KEY (stock_take_id) REFERENCES stock_takes (id),
    FOREIGN KEY (inventory_id) REFERENCES inventory (id),

    INDEX (inventory_id)
);

-- Menu Module
-- -----------------

CREATE TABLE IF NOT EXISTS menu_categories
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    name       VARCHAR(100)            NOT NULL,
    parent_id  INT,
    active     BOOLEAN                          DEFAULT TRUE,
    station    ENUM ('KITCHEN', 'BAR') NOT NULL DEFAULT 'KITCHEN',
    created_at TIMESTAMP                        DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP                        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (parent_id) REFERENCES menu_categories (id)
);

CREATE TABLE IF NOT EXISTS menu_items
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(150)   NOT NULL,
    description TEXT,
    price       DECIMAL(12, 2) NOT NULL,
    category_id INT,
    is_combo    BOOLEAN   DEFAULT FALSE,
    active      BOOLEAN   DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id) REFERENCES menu_categories (id),

    INDEX (name),
    INDEX (active)
);

CREATE TABLE IF NOT EXISTS menu_item_ingredients
(
    menu_item_id INT            NOT NULL,
    inventory_id INT            NOT NULL,
    quantity     DECIMAL(10, 2) NOT NULL,
    unit         VARCHAR(50)    NOT NULL,

    PRIMARY KEY (menu_item_id, inventory_id),

    FOREIGN KEY (menu_item_id) REFERENCES menu_items (id),
    FOREIGN KEY (inventory_id) REFERENCES inventory (id)
);

-- Front House
-- ---------------------

CREATE TABLE IF NOT EXISTS tables
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    number     INT UNIQUE,
    capacity   INT       NOT NULL,
    status     ENUM ('AVAILABLE', 'OCCUPIED', 'RESERVED', 'OUT_OF_SERVICE') DEFAULT 'AVAILABLE',
    created_at TIMESTAMP NOT NULL                                           DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL                                           DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX (status)
);

-- Orders Module
-- -----------------

CREATE TABLE IF NOT EXISTS orders
(
    id           INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50)                                  NOT NULL UNIQUE,
    status       ENUM ('PLACED','SERVED','PAYED','CANCELLED') NOT NULL,
    type         ENUM ('DINE_IN','TAKEAWAY','DELIVERY')       NOT NULL,
    staff_id     INT                                          NOT NULL,
    table_id     INT,
    total_amount DECIMAL(12, 2)                               NOT NULL,
    closed_at    TIMESTAMP,
    created_at   TIMESTAMP                                    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP                                    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (staff_id) REFERENCES staff (id),
    FOREIGN KEY (table_id) REFERENCES tables (id),

    INDEX (status),
    INDEX (created_at),
    INDEX (staff_id, created_at) -- *
);

CREATE TABLE IF NOT EXISTS order_items
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    order_id      INT,
    menu_item_id  INT,
    price_at_time DECIMAL(12, 2) NOT NULL,
    quantity      INT            NOT NULL,
    served        TINYINT(1)     NOT NULL DEFAULT 0,
    created_at    TIMESTAMP               DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders (id),
    FOREIGN KEY (menu_item_id) REFERENCES menu_items (id),

    INDEX (created_at)
);

-- Payments Module
-- -------------------

CREATE TABLE IF NOT EXISTS payments
(
    id               INT PRIMARY KEY AUTO_INCREMENT,
    transaction_code VARCHAR(100),
    method           ENUM ('CASH', 'CARD', 'MOBILE', 'REFUND') NOT NULL,
    amount           DECIMAL(10, 2)                            NOT NULL,
    order_id         INT                                       NOT NULL,
    cashier_id       INT                                       NOT NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders (id),
    FOREIGN KEY (cashier_id) REFERENCES staff (id),

    INDEX (created_at),
    INDEX (method)
);

-- System Module
-- --------------------

CREATE TABLE IF NOT EXISTS audit_logs
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    action     VARCHAR(255) NOT NULL,
    method     VARCHAR(10)  NOT NULL DEFAULT 'GET',
    url        VARCHAR(255) NOT NULL DEFAULT '',
    ip_address VARCHAR(45),
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES staff (id),
    INDEX (created_at)
);


-- Report Views
-- --------------------

CREATE OR REPLACE VIEW v_sales_by_day AS
SELECT DATE(o.created_at)               AS day,
       COUNT(*)                         AS orders,
       (SELECT COALESCE(SUM(oi.quantity), 0)
        FROM order_items oi
        WHERE oi.order_id = o.id)       AS items,
       COALESCE(SUM(o.total_amount), 0) AS revenue,
       COALESCE(SUM(CASE
                        WHEN EXISTS (SELECT 1 FROM payments p WHERE p.order_id = o.id)
                            THEN o.total_amount
                        ELSE 0 END), 0) AS paid,
       COALESCE(SUM(CASE
                        WHEN NOT EXISTS (SELECT 1 FROM payments p WHERE p.order_id = o.id)
                            THEN o.total_amount
                        ELSE 0 END), 0) AS unpaid
FROM orders o
WHERE o.status <> 'CANCELLED'
GROUP BY DATE(o.created_at);

CREATE OR REPLACE VIEW v_item_sales_by_day AS
SELECT DATE(o.created_at)                  AS day,
       mi.name                             AS item,
       COALESCE(mc.name, 'Uncategorised')  AS category,
       SUM(oi.quantity)                    AS quantity,
       SUM(oi.quantity * oi.price_at_time) AS revenue
FROM order_items oi
         INNER JOIN menu_items mi ON mi.id = oi.menu_item_id
         LEFT JOIN menu_categories mc ON mc.id = mi.category_id
         INNER JOIN orders o ON o.id = oi.order_id
WHERE o.status <> 'CANCELLED'
GROUP BY DATE(o.created_at), mi.id, mi.name, mc.name;

CREATE OR REPLACE VIEW v_payments_by_day AS
SELECT DATE(created_at) AS day,
       method,
       COUNT(*)         AS count,
       SUM(amount)      AS total
FROM payments
GROUP BY DATE(created_at), method;

-- Business Days / Day Closure
-- --------------------

CREATE TABLE IF NOT EXISTS business_days
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    date          DATE          NOT NULL UNIQUE,
    is_closed     TINYINT(1)    NOT NULL DEFAULT 0,
    opened_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    closed_at     TIMESTAMP     NULL,
    closed_by     INT           NULL,
    order_count   INT           NOT NULL DEFAULT 0,
    item_count    INT           NOT NULL DEFAULT 0,
    gross_total   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    paid_total    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    unpaid_total  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    cash_total    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    card_total    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    mobile_total  DECIMAL(12,2) NOT NULL DEFAULT 0.00,

    FOREIGN KEY (closed_by) REFERENCES staff (id),

    INDEX (is_closed)
);

-- Restaurant settings (used on receipts/bills)
-- --------------------

CREATE TABLE IF NOT EXISTS restaurant_details
(
    id       INT AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(120) NOT NULL DEFAULT 'RMS Restaurant',
    address  VARCHAR(255) NOT NULL DEFAULT '',
    phone    VARCHAR(30)  NOT NULL DEFAULT '',
    currency VARCHAR(10)  NOT NULL DEFAULT 'KES'
);
