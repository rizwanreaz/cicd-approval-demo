<?php

use PHPUnit\Framework\TestCase;

final class AuthTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT UNIQUE, password_hash TEXT)');

        $stmt = $this->pdo->prepare('INSERT INTO users (username, password_hash) VALUES (:username, :hash)');
        $stmt->execute([
            'username' => 'admin',
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
