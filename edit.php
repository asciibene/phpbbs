<?php
require __DIR__ . '/scripts/funcdefs.php';

ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
}

if (!is_logged_in()) {
    echo printError('you are not logged in.');
} elseif (isset($_GET['index']) && empty($_POST) && ($_GET['a'] ?? '') === 'editpage') {
    echo '<h3>Editing page: <u>' . e(gettitlefromindex((string) $_GET['index'])) . '</u></h3>';
    echo '<form method="post" action="./edit.php?a=wr&index=' . e($_GET['index']) . '">';
    echo csrf_field();
    echo '<textarea name="edit_content">' . e(getcontent_nl2br((string) $_GET['index'])) . '</textarea><br>';
    echo '<button type="submit">Send</button>';
    echo '</form>';
} elseif (isset($_GET['index']) && !empty($_POST['edit_content']) && ($_GET['a'] ?? '') === 'wr') {
    editpagetext((string) $_GET['index'], trim((string) $_POST['edit_content']), current_user()['username']);
    echo notify('Page updated');
} elseif (isset($_GET['index']) && ($_GET['a'] ?? '') === 'viewhistory') {
    echo '<h1>Showing edit history for page: ' . e(gettitlefromindex((string) $_GET['index'])) . '</h1>';
    showedits((string) $_GET['index']);
}

$body = ob_get_clean();
echo render_layout('phpwiki - edit', $body);
