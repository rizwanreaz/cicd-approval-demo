<?php

final class Auth
{
    public static function attempt(PDO $pdo, string $username, string $password): ?array
    {
        if ($username === '' || $password === '') {
            return null;
        }

        $stmt = $pdo->prepare('SELECT id, username, email, password_hash FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            return ['id' => $user['id'], 'username' => $user['username'], 'email' => $user['email']];
        }

        return null;
    }
}
