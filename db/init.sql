-- Sample schema and seed data for the customers/orders demo database

DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS customers;

CREATE TABLE customers (
    customer_id SERIAL PRIMARY KEY,
    first_name  VARCHAR(50) NOT NULL,
    last_name   VARCHAR(50) NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE orders (
    order_id     SERIAL PRIMARY KEY,
    customer_id  INTEGER NOT NULL REFERENCES customers(customer_id),
    order_date   DATE NOT NULL DEFAULT CURRENT_DATE,
    total_amount NUMERIC(10, 2) NOT NULL CHECK (total_amount >= 0),
    status       VARCHAR(20) NOT NULL DEFAULT 'pending'
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
