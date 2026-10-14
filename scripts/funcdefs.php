<?php
session_start();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function csrf_token() {
    return $_SESSION['csrf'] ?? '';
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '" />';
}

function require_csrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(403);
        die('CSRF validation failed.');
    }
}

function session_security() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
}

function username_is_valid($username) {
    $username = trim((string) $username);
    return preg_match('/^[a-zA-Z0-9_]{3,24}$/', $username) === 1;
}

function password_is_valid($password) {
    return strlen((string) $password) >= 6;
}

function logout_user() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: /index.php');
    exit;
}

function require_login($redirect = '/login.php') {
    if (!Auth::isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}

session_security();

require_once __DIR__ . '/../src/Storage.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Forum.php';

function render_layout($title, $body) {
    $menuLinks = '<a href="/login.php">Login</a>'
        . '<a href="/register.php">Register</a>'
        . '<a href="/new.php">New post</a>'
        . '<a href="/docs/help.html">Help</a>';

    if (Auth::isLoggedIn()) {
        $menuLinks .= '<a href="/logout.php">Logout</a>';
    }

    $menu = '<div class="menu">' . $menuLinks . '</div>';
    $header = '<header class="site-header">'
        . '<div>'
        . '<h1 id="logo1">phpBBS</h1>'
        . '<div id="logover">bulletin board</div>'
        . '</div>'
        . $menu
        . '</header>';

    return <<<HTML
<html>
<head>
    <title>{$title}</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
    <div class="container">
        {$header}
        <div class="card">
            {$body}
        </div>
    </div>
</body>
</html>
HTML;
}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function notify($message) {
    return '<div class="notify">' . e($message) . '</div>';
}

function printError($message) {
    return '<small class="error">' . e($message) . '</small>';
}

function current_user() {
    return Auth::currentUser();
}

function is_logged_in() {
    return Auth::isLoggedIn();
}

function login_user($username, $password) {
    return Auth::login($username, $password);
}

function register_user($username, $password) {
    return Auth::register($username, $password);
}

function list_posts() {
    return Forum::listPosts();
}

function create_post($title, $content, $author) {
    return Forum::createPost($title, $content, $author);
}

function addvote($id, $value) {
    return Forum::addVote($id, (int) $value);
}

function addcomment($id, $comment, $author = null) {
    $author = $author ?: (Auth::currentUser()['username'] ?? 'anon');
    return Forum::addComment($id, $comment, $author);
}

function editpagetext($id, $content, $editor = null) {
    $editor = $editor ?: (Auth::currentUser()['username'] ?? 'anon');
    return Forum::editPost($id, $content, $editor);
}

function viewpage($id, $showVotes = true) {
    $post = Forum::findPostById($id);
    if (!$post) {
        echo printError('Page not found');
        return false;
    }

    echo '<div class="view_title">' . e($post['title']) . '</div>';
    echo '<div class="view_time">' . date('Y-m-d H:i', $post['created_at']) . '</div>';
    echo '<div class="view_view">' . nl2br(e($post['content'])) . '</div>';

    if ($showVotes) {
        echo '<div class="vote">Votes: ' . (int) ($post['votes'] ?? 0) . '</div>';
        echo '<form method="post">' . csrf_field() . '<button name="voted" value="1">Upvote</button></form>';
    }

    if (!empty($post['comments'])) {
        echo '<h4>Comments</h4>';
        foreach ($post['comments'] as $comment) {
            echo '<div class="com">';
            echo '<span class="com_auth">' . e($comment['author']) . '</span>';
            echo '<span class="com_tstamp">' . date('Y-m-d H:i', (int) $comment['ts']) . '</span>';
            echo '<div>' . nl2br(e($comment['content'])) . '</div>';
            echo '</div>';
        }
    }

    return true;
}

function showedits($id) {
    $post = Forum::findPostById($id);
    if (!$post) {
        echo printError('Page not found');
        return;
    }
    if (empty($post['edits'])) {
        echo '<div>No edits</div>';
        return;
    }
    foreach ($post['edits'] as $edit) {
        echo '<div class="com">';
        echo '<b>' . e($edit['editor']) . '</b>';
        echo '<span class="tsdiff">' . date('Y-m-d H:i', (int) $edit['ts']) . '</span>';
        echo '<div>' . nl2br(e($edit['content'])) . '</div>';
        echo '</div>';
    }
}

function gettitlefromindex($id) {
    $post = Forum::findPostById($id);
    return $post ? $post['title'] : '';
}

function getcontent_nl2br($id) {
    $post = Forum::findPostById($id);
    return $post ? $post['content'] : '';
}

function viewprofile($lookup) {
    $user = Auth::findUserByLookup($lookup);
    if (!$user) {
        echo printError('User not found');
        return false;
    }
    echo '<h2>' . e($user['username']) . '</h2>';
    echo '<div>Joined: ' . date('Y-m-d', (int) $user['created_at']) . '</div>';
    return true;
}

function verifylogin() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }
    $username = $_POST['login_name'] ?? '';
    $password = $_POST['login_password'] ?? '';
    return Auth::login($username, $password);
}

function checksec($uindex = null, $hash = null) {
    return Auth::isLoggedIn();
}

?>
