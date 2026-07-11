-- Schema and seed data for the login demo (MySQL)

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
