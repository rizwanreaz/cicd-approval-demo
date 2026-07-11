-- Schema and seed data for the login demo (MySQL)

DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Dummy accounts for the demo login page.
-- admin / admin123
-- demo  / demo123
INSERT INTO users (username, password_hash) VALUES
    ('admin', '$2b$10$jNeFPLa2LmhGoLLD5wC6EO24oURWsmwZdhFjamkxwPBfkNwruNpG6'),
    ('demo',  '$2b$10$GKrGMbub.PlmzeA7YL2Iz.yFadDxc4DcUzDL8DzPdqRqE06A5su/.');

CREATE TABLE customers (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name  VARCHAR(50) NOT NULL,
    email      VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE orders (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    customer_id  INT NOT NULL,
    order_date   DATE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL CHECK (total_amount >= 0),
    status       VARCHAR(20) NOT NULL DEFAULT 'pending',
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

INSERT INTO customers (first_name, last_name, email) VALUES
    ('Ada',       'Lovelace', 'ada.lovelace@example.com'),
    ('Grace',     'Hopper',   'grace.hopper@example.com'),
    ('Alan',      'Turing',   'alan.turing@example.com'),
    ('Katherine', 'Johnson',  'katherine.johnson@example.com');

INSERT INTO orders (customer_id, order_date, total_amount, status) VALUES
    (1, '2026-01-05', 129.99, 'completed'),
    (1, '2026-02-14',  45.50, 'completed'),
    (2, '2026-03-01', 899.00, 'shipped'),
    (3, '2026-03-10',  19.99, 'pending'),
    (4, '2026-03-12', 250.75, 'completed');
