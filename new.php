<?php
require __DIR__ . '/scripts/funcdefs.php';

if (!is_logged_in()) {
    echo render_layout('phpwiki - new post', printError('You are not logged in. <a href="/login.php">Login</a>'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['user_title'])) {
    require_csrf();
    $title = trim((string) $_POST['user_title']);
    $content = trim((string) ($_POST['user_content'] ?? ''));
    $id = create_post($title, $content, current_user()['username']);
    header('Location: /view.php?index=' . urlencode((string) $id));
    exit;
}

$body = '<h3>New post</h3>'
    . '<form method="post" action="./new.php">'
    . csrf_field()
    . '<input type="text" name="user_title" placeholder="Title" /><br>'
    . '<textarea id="cnt" name="user_content">default text</textarea><br>'
    . '<button type="submit">Send</button>'
    . '</form>';

echo render_layout('phpwiki - new post', $body);
