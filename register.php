<?php
require __DIR__ . '/scripts/funcdefs.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reg') {
    require_csrf();
    if (register_user($_POST['reg_name'] ?? '', $_POST['reg_password'] ?? '')) {
        $body = notify('Registration success');
    } else {
        $body = printError('Registration failed. Please choose a different username or enter a password.');
    }
} else {
    $body = '<h3>Registration Form</h3>'
        . '<form method="post" action="./register.php">'
        . csrf_field()
        . '<input type="text" name="reg_name" placeholder="username" /><br>'
        . '<input type="password" name="reg_password" placeholder="pwd" /><br>'
        . '<input type="radio" id="captcha_yes" name="reg_captcha" value="yes"><label for="captcha_yes">Yes</label>'
        . '<input type="radio" id="captcha_no" name="reg_captcha" value="no"><label for="captcha_no">no</label>'
        . '<button type="submit" name="action" value="reg">Send</button>'
        . '</form>';
}

echo render_layout('phpbbs - registration', $body);
