<?php
require_once __DIR__ . '/scripts/funcdefs.php';

if (!Auth::isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

logout_user();
