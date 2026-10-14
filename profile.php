<?php
require __DIR__ . '/scripts/funcdefs.php';

ob_start();
if (isset($_GET['uindex'])) {
    viewprofile((string) $_GET['uindex']);
} elseif (empty($_GET)) {
    echo printError('No user index supplied');
}
$body = ob_get_clean();
echo render_layout('phpwiki', $body);
