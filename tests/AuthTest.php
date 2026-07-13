<?php

use PHPUnit\Framework\TestCase;

final class AuthTest extends TestCase
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

    public function testValidCredentialsReturnTheUser(): void
    {
        $user = Auth::attempt($this->pdo, 'admin', 'admin123');

        $this->assertNotNull($user);
        $this->assertSame('admin', $user['username']);
    }

    public function testWrongPasswordIsRejected(): void
    {
        $this->assertNull(Auth::attempt($this->pdo, 'admin', 'wrong-password'));
    }

    public function testUnknownUsernameIsRejected(): void
    {
        $this->assertNull(Auth::attempt($this->pdo, 'nobody', 'admin123'));
    }

    public function testEmptyCredentialsAreRejected(): void
    {
        $this->assertNull(Auth::attempt($this->pdo, '', ''));
        $this->assertNull(Auth::attempt($this->pdo, 'admin', ''));
        $this->assertNull(Auth::attempt($this->pdo, '', 'admin123'));
    }
}
