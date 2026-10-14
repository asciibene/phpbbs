<?php
require __DIR__ . '/scripts/funcdefs.php';

ob_start();
if (is_logged_in()) {
    echo '<h1>Admin</h1>';
    echo '<div>Admin area placeholder</div>';
} else {
    echo printError('You are not logged in.');
}
$body = ob_get_clean();
echo render_layout('phpbbs - admin', $body);
