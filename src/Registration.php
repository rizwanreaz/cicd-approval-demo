<?php

final class Registration
{
    private const USERNAME_MIN = 3;
    private const USERNAME_MAX = 30;
    private const PASSWORD_MIN = 8;

    /**
     * Validates registration input and, if valid, inserts the new user.
     * Returns ['ok' => true, 'user' => [...]] or ['ok' => false, 'errors' => [...]].
     *
     * @return array{ok: bool, user?: array, errors?: array<string>}
     */
    public static function register(
        PDO $pdo,
        string $username,
        string $email,
        string $password,
        string $passwordConfirm
    ): array {
        $errors = self::validate($username, $email, $password, $passwordConfirm);

        if ($errors !== []) {
            return ['ok' => false, 'errors' => $errors];
        }

        $username = trim($username);
        $email = trim($email);

        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username OR email = :email');
        $stmt->execute(['username' => $username, 'email' => $email]);

        if ($stmt->fetch()) {
            return ['ok' => false, 'errors' => ['That username or email is already registered.']];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $insert = $pdo->prepare(
            'INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :hash)'
        );
        $insert->execute(['username' => $username, 'email' => $email, 'hash' => $hash]);

        return [
            'ok' => true,
            'user' => [
                'id' => (int) $pdo->lastInsertId(),
                'username' => $username,
                'email' => $email,
            ],
        ];
    }

    /**
     * @return array<string> list of validation error messages (empty if valid)
     */
    public static function validate(string $username, string $email, string $password, string $passwordConfirm): array
    {
        $errors = [];
        $username = trim($username);
        $email = trim($email);

        if (mb_strlen($username) < self::USERNAME_MIN || mb_strlen($username) > self::USERNAME_MAX) {
            $errors[] = sprintf(
                'Username must be between %d and %d characters.',
                self::USERNAME_MIN,
                self::USERNAME_MAX
            );
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username may only contain letters, numbers, and underscores.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (mb_strlen($password) < self::PASSWORD_MIN) {
            $errors[] = sprintf('Password must be at least %d characters.', self::PASSWORD_MIN);
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }

        return $errors;
    }
}
