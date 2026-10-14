<?php
require __DIR__ . '/scripts/funcdefs.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    require_csrf();
    if (login_user($_POST['login_name'] ?? '', $_POST['login_password'] ?? '')) {
        $_SESSION['user']['last_login'] = time();
        $body = notify('now logged in');
    } else {
        $body = printError('Invalid username or password.');
    }
} else {
    $body = <<<HTML
<h3>Login</h3>
<form method="post" action="./login.php">
    <input type="hidden" name="csrf_token" value="{$_SESSION['csrf']}" />
    <input type="text" name="login_name" placeholder="username" />
    <br />
    <input type="password" name="login_password" placeholder="password" />
    <br>
    <button name="action" value="login" type="submit">Login</button>
</form>
HTML;
}

echo render_layout('phpwiki - login', $body);
