-- Staff Module
-- ---------------------
CREATE TABLE IF NOT EXISTS roles
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS permissions
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
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
    id         INT PRIMARY KEY AUTO_INCREMENT,
    name       VARCHAR(100) NOT NULL UNIQUE,
    unit       VARCHAR(50)  NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS inventory_movements
(
    id             INT PRIMARY KEY AUTO_INCREMENT,
    inventory_id   INT                              NOT NULL,
    movement_type  ENUM ('IN', 'OUT', 'ADJUSTMENT') NOT NULL,
    quantity       DECIMAL(10, 2)                   NOT NULL,
    reference_type ENUM ('ORDER', 'MANUAL', 'STOCK_TAKE'),
    reference_id   INT,
    performed_by   INT                              NOT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (inventory_id) REFERENCES inventory (id),
    FOREIGN KEY (performed_by) REFERENCES staff (id),

    INDEX (created_at),
    INDEX (movement_type),
    INDEX (reference_type, reference_id) -- *
);

-- Menu Module
-- -----------------

CREATE TABLE IF NOT EXISTS menu_categories
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    name       VARCHAR(100) NOT NULL,
    parent_id  INT,
    active     BOOLEAN   DEFAULT TRUE,
    station    ENUM('KITCHEN', 'BAR') NOT NULL DEFAULT 'KITCHEN',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

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
    order_number VARCHAR(50)                                    NOT NULL UNIQUE,
    status       ENUM ('PLACED','SERVED','PAYED','CANCELLED') NOT NULL,
    type         ENUM ('DINE_IN','TAKEAWAY','DELIVERY')         NOT NULL,
    staff_id      INT                                            NOT NULL,
    table_id     INT,
    total_amount DECIMAL(12, 2)                                 NOT NULL,
    closed_at    TIMESTAMP,
    created_at   TIMESTAMP                                      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP                                      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

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
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

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

-- Booking Module
-- ------------------
CREATE TABLE IF NOT EXISTS reservation
(
    id            INT PRIMARY KEY AUTO_INCREMENT,
    staff_id       INT          NOT NULL,
    table_id      INT          NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    reservation_time  TIMESTAMP    NOT NULL,
    status        ENUM ( 'CANCELLED', 'PAID') DEFAULT 'PAID',
    payment_id    INT              NOT NULL ,
    created_at    TIMESTAMP                                               DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP                                               DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (staff_id) REFERENCES staff (id),
    FOREIGN KEY (table_id) REFERENCES tables (id),
    FOREIGN KEY (payment_id) REFERENCES payments (id),

    INDEX (reservation_time),
    INDEX (status)
);

-- System Module
-- --------------------

CREATE TABLE IF NOT EXISTS audit_logs
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    staff_id    INT          NOT NULL,
    action     VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (staff_id) REFERENCES staff (id),
    INDEX (created_at)
);

-- License / Subscription Module
-- ----------------------------------

CREATE TABLE IF NOT EXISTS setup_progress -- get better name
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    state       ENUM ('FRESH', 'ACTIVATED', 'LOCKED') NOT NULL DEFAULT 'FRESH',
    instance_id CHAR(36)                              NOT NULL UNIQUE,
    created_at  TIMESTAMP                                      DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP                                      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX (state)
);

CREATE TABLE IF NOT EXISTS license
(
    id               INT AUTO_INCREMENT PRIMARY KEY,
    license_key      TEXT      NOT NULL,
    edition          ENUM ('SMALL', 'MEDIUM')              DEFAULT 'SMALL',
    max_users        INT                                   DEFAULT 5,
    expiry_date      DATE      NOT NULL,
    status           ENUM ('ACTIVE', 'EXPIRED', 'REVOKED') DEFAULT 'ACTIVE',
    created_at       TIMESTAMP                             DEFAULT CURRENT_TIMESTAMP,
    last_verified_at TIMESTAMP NULL,

    INDEX (license_key),
    INDEX (edition),
    INDEX (status)
);

CREATE TABLE IF NOT EXISTS restaurant_details
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(200) NOT NULL,
    address    TEXT,
    phone      VARCHAR(50),
    email      VARCHAR(100),
    currency   VARCHAR(10) DEFAULT 'KES',
    created_by INT          NOT NULL,
    created_at TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (created_by) REFERENCES staff (id),
    INDEX (name)
);
