<?php
require __DIR__ . '/scripts/funcdefs.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    if (isset($_POST['voted']) && isset($_GET['index']) && is_logged_in()) {
        addvote((string) $_GET['index'], $_POST['voted']);
    } elseif (isset($_POST['voted']) && !is_logged_in()) {
        $voteError = printError('You are not logged in.');
    }

    if (isset($_POST['user_comment']) && !empty($_POST['user_comment']) && is_logged_in()) {
        addcomment((string) $_GET['index'], $_POST['user_comment'], current_user()['username']);
    } elseif (isset($_POST['user_comment']) && !is_logged_in()) {
        $commentError = printError('You are not logged in.');
    }
}

if (!isset($_GET['index'])) {
    echo render_layout('phpwiki', printError('No page index supplied.'));
    exit;
}

ob_start();
viewpage((string) $_GET['index'], true);
if (!empty($voteError)) echo $voteError;
if (!empty($commentError)) echo $commentError;

echo '<form method="post" action="./view.php?index=' . e($_GET['index']) . '">';
echo csrf_field();
echo '<h3>Add a comment</h3>';
echo '<input type="text" name="user_comment" placeholder="add a comment" />';
echo '<br><button type="submit">Submit</button>';
echo '</form>';

$body = ob_get_clean();
echo render_layout('phpwiki', $body);
