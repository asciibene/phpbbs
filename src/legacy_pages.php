<?php
require_once __DIR__ . '/../scripts/funcdefs.php';

function render_layout($title, $body) {
    return <<<HTML
<html>
<head>
    <title>{$title}</title>
    <link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
    {$body}
</body>
</html>
HTML;
}

function login_page() {
    $body = '';
    if (empty($_POST)) {
        $body = <<<HTML
<h3>Login</h3>
<form method="post" action="./login.php">
    <input type="text" name="login_name" placeholder="username" />
    <br />
    <input type="password" name="login_password" placeholder="password" />
    <br>
    <button name="action" value="login" type="submit">Login</button>
</form>
HTML;
    } elseif ($_POST['action'] ?? '' === 'login' && verifylogin()) {
        ob_start();
        notify('now logged in');
        $body = ob_get_clean();
    }

    return render_layout('phpwiki - login', $body);
}

function register_page() {
    $body = '';
    if (empty($_POST)) {
        $body = <<<HTML
<h3>Registration Form</h3>
<form method="post" action="./register.php">
    <input type="text" name="reg_name" placeholder="username" />
    <br>
    <input type="password" name="reg_password" placeholder="pwd" />
    <br>
    <input type="radio" id="captcha_yes" name="reg_captcha" value="yes">
    <label for="captcha_yes">Yes</label>
    <input type="radio" id="captcha_no" name="reg_captcha" value="no">
    <label for="captcha_no">no</label>
    <button type="submit" name="action" value="reg">Send</button>
</form>
HTML;
    } elseif (($_POST['action'] ?? '') === 'reg' && $db->new_user() === true) {
        ob_start();
        notify('Registration success');
        $body = ob_get_clean();
    }

    return render_layout('phpbbs - registration', $body);
}

function view_page() {
    ob_start();

    if (isset($_POST['voted']) && isset($_GET['index']) && checksec($_COOKIE['uindex'] ?? null, $_COOKIE['hash'] ?? null)) {
        addvote($_GET['index'], $_POST['voted']);
    } elseif (!checksec($_COOKIE['uindex'] ?? null, $_COOKIE['hash'] ?? null) && isset($_POST['voted'])) {
        printError('You are not logged in.');
    }

    if (isset($_POST['user_comment']) && checksec($_COOKIE['uindex'] ?? null, $_COOKIE['hash'] ?? null)) {
        addcomment($_GET['index'], $_POST['user_comment']);
    } elseif (!checksec($_COOKIE['uindex'] ?? null, $_COOKIE['hash'] ?? null) && isset($_POST['user_comment'])) {
        printError('You are not logged in.');
    }

    if (isset($_GET['index'])) {
        viewpage($_GET['index'], !isset($_POST['voted']));
        echo '<form method="post" action="./view.php?index=' . htmlspecialchars($_GET['index']) . '">';
        echo '<h3>Add a comment</h3>';
        echo '<input type="text" name="user_comment" placeholder="add a comment" />';
        echo '<br>';
        echo '<button type="submit">Submit</button>';
        echo '</form>';
    }

    $body = ob_get_clean();
    return render_layout('phpwiki', $body);
}

function edit_page() {
    ob_start();

    if (checksec($_COOKIE['uindex'] ?? null, $_COOKIE['hash'] ?? null) === false) {
        printError('you are not logged in.');
    } elseif (checksec($_COOKIE['uindex'] ?? null, $_COOKIE['hash'] ?? null) && isset($_GET['index']) && empty($_POST) && ($_GET['a'] ?? '') === 'editpage') {
        echo '<h3>Editing page: <u>' . htmlspecialchars(gettitlefromindex($_GET['index'])) . '</u></h3>';
        echo '<form method="post" action="./edit.php?a=wr&index=' . htmlspecialchars($_GET['index']) . '">';
        echo '<textarea name="edit_content">' . htmlspecialchars(getcontent_nl2br($_GET['index'])) . '</textarea><br>';
        echo '<button type="submit">Send</button>';
        echo '</form>';
    } elseif (isset($_COOKIE['verified']) && isset($_GET['index']) && !empty($_POST['edit_content']) && ($_GET['a'] ?? '') === 'wr') {
        editpagetext($_GET['index'], $_POST['edit_content']);
    } elseif (isset($_COOKIE['verified']) && isset($_GET['index']) && ($_GET['a'] ?? '') === 'viewhistory') {
        echo '<h1>Showing edit history for page: ' . htmlspecialchars(gettitlefromindex($_GET['index'])) . '</h1>';
        showedits($_GET['index']);
    }

    $body = ob_get_clean();
    return render_layout('phpwiki - edit', $body);
}

function profile_page() {
    ob_start();

    if (isset($_GET['uindex'])) {
        viewprofile($_GET['uindex']);
    } elseif (empty($_GET)) {
        printError('No user index supplied');
    }

    $body = ob_get_clean();
    return render_layout('phpwiki', $body);
}

function admin_page() {
    ob_start();

    if (checksec()) {
        echo '<h1>Admin</h1>';
        echo '<div>Admin area placeholder</div>';
    }

    $body = ob_get_clean();
    return render_layout('phpbbs - admin', $body);
}
