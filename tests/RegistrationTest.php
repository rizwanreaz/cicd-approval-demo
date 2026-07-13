<?php

use PHPUnit\Framework\TestCase;

final class RegistrationTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec(
            'CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT UNIQUE, email TEXT UNIQUE, password_hash TEXT)'
        );

        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :hash)'
        );
        $stmt->execute([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'hash' => password_hash('admin123', PASSWORD_BCRYPT),
        ]);
    }

    public function testValidRegistrationSucceeds(): void
    {
        $result = Registration::register($this->pdo, 'newuser', 'new@example.com', 'password123', 'password123');

        $this->assertTrue($result['ok']);
        $this->assertSame('newuser', $result['user']['username']);
        $this->assertSame('new@example.com', $result['user']['email']);
    }

    public function testPasswordIsHashedNotStoredInPlainText(): void
    {
        Registration::register($this->pdo, 'newuser', 'new@example.com', 'password123', 'password123');

        $stmt = $this->pdo->prepare('SELECT password_hash FROM users WHERE username = :username');
        $stmt->execute(['username' => 'newuser']);
        $hash = $stmt->fetchColumn();

        $this->assertNotSame('password123', $hash);
        $this->assertTrue(password_verify('password123', $hash));
    }

    public function testUsernameTooShortIsRejected(): void
    {
        $result = Registration::register($this->pdo, 'ab', 'ab@example.com', 'password123', 'password123');

        $this->assertFalse($result['ok']);
        $this->assertNotEmpty($result['errors']);
    }

    public function testUsernameWithInvalidCharactersIsRejected(): void
    {
        $result = Registration::register($this->pdo, 'bad name!', 'bad@example.com', 'password123', 'password123');

        $this->assertFalse($result['ok']);
    }

    public function testInvalidEmailIsRejected(): void
    {
        $result = Registration::register($this->pdo, 'gooduser', 'not-an-email', 'password123', 'password123');

        $this->assertFalse($result['ok']);
    }

    public function testShortPasswordIsRejected(): void
    {
        $result = Registration::register($this->pdo, 'gooduser', 'good@example.com', 'short', 'short');

        $this->assertFalse($result['ok']);
    }

    public function testMismatchedPasswordsAreRejected(): void
    {
        $result = Registration::register($this->pdo, 'gooduser', 'good@example.com', 'password123', 'password456');

        $this->assertFalse($result['ok']);
    }

    public function testDuplicateUsernameIsRejected(): void
    {
        $result = Registration::register($this->pdo, 'admin', 'different@example.com', 'password123', 'password123');

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('already registered', $result['errors'][0]);
    }

    public function testDuplicateEmailIsRejected(): void
    {
        $result = Registration::register($this->pdo, 'differentuser', 'admin@example.com', 'password123', 'password123');

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('already registered', $result['errors'][0]);
    }
}
