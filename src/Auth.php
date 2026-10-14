<?php
require_once __DIR__ . '/Storage.php';

class Auth {
    public static function currentUser() {
        return $_SESSION['user'] ?? null;
    }

    public static function isLoggedIn() {
        return !empty($_SESSION['user']);
    }

    public static function findUserByLookup($lookup) {
        $users = Storage::read('users');
        if (is_numeric($lookup)) {
            $idx = (int) $lookup;
            return $users[$idx] ?? null;
        }

        foreach ($users as $user) {
            if (strcasecmp((string) $user['username'], (string) $lookup) === 0) {
                return $user;
            }
        }

        return null;
    }

    public static function register($username, $password) {
        $username = trim((string) $username);
        $password = (string) $password;

        if (!username_is_valid($username) || !password_is_valid($password)) {
            return false;
        }

        $users = Storage::read('users');
        foreach ($users as $user) {
            if (strcasecmp((string) $user['username'], $username) === 0) {
                return false;
            }
        }

        $users[] = [
            'id' => count($users) + 1,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => time(),
        ];

        Storage::write('users', $users);
        return true;
    }

    public static function login($username, $password) {
        $username = trim((string) $username);
        $password = (string) $password;

        if (!username_is_valid($username) || !password_is_valid($password)) {
            return false;
        }

        $users = Storage::read('users');
        foreach ($users as $index => $user) {
            if (strcasecmp((string) $user['username'], $username) === 0 && password_verify($password, (string) $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => (int) $user['id'],
                    'username' => $user['username'],
                    'index' => $index,
                ];
                return true;
            }
        }

        return false;
    }
}
