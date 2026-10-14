<?php
// Minimal compatibility layer to stabilize legacy phpBBS prototype.
// Provides simple JSON-backed storage and a small API used by existing pages.

session_start();

define('DATA_DIR', __DIR__ . '/../data');
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

function data_file($name) {
    return DATA_DIR . '/' . $name . '.json';
}

function load_data($name) {
    $file = data_file($name);
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function save_data($name, $data) {
    $file = data_file($name);
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

class DB {
    public $users;
    public $posts;

    public function __construct() {
        $this->users = load_data('users');
        $this->posts = load_data('posts');
    }

    // Save current data back to disk
    public function savedb() {
        save_data('users', $this->users);
        save_data('posts', $this->posts);
    }

    // Create a new user from $_POST['reg_name'] / ['reg_password']
    public function new_user() {
        $name = trim($_POST['reg_name'] ?? '');
        $pwd = $_POST['reg_password'] ?? '';
        if ($name === '' || $pwd === '') {
            return false;
        }
        // simple uniqueness check
        foreach ($this->users as $u) {
            if ($u['name'] === $name) return false;
        }
        $user = [
            'name' => $name,
            'password' => password_hash($pwd, PASSWORD_DEFAULT),
            'created_at' => time(),
        ];
        $this->users[] = $user;
        $this->savedb();
        return true;
    }
}

// global DB instance used by legacy pages
$db = new DB();

// Basic helpers
function notify($msg) {
    echo '<div class="notify">' . htmlspecialchars($msg) . '</div>'; 
}

function printError($msg) {
    echo '<small class="error">' . htmlspecialchars($msg) . '</small>';
}

function initdb() {
    global $db;
    if (!$db) {
        $db = new DB();
    }
    return true;
}

// Authentication helpers
function verifylogin() {
    global $db;
    $name = $_POST['login_name'] ?? '';
    $pwd = $_POST['login_password'] ?? '';
    if ($name === '' || $pwd === '') return false;
    foreach ($db->users as $idx => $u) {
        if ($u['name'] === $name && password_verify($pwd, $u['password'])) {
            // set session and a simple cookie for legacy code
            $_SESSION['user'] = ['name' => $name, 'index' => $idx];
            setcookie('uindex', (string)$idx, 0, '/');
            setcookie('hash', bin2hex(random_bytes(10)), 0, '/');
            // store a simple verified flag
            setcookie('verified', '1', 0, '/');
            return true;
        }
    }
    return false;
}

function checksec($uindex = null, $hash = null) {
    // Prefer session-based auth, fall back to cookies (legacy)
    if (isset($_SESSION['user'])) return true;
    if ($uindex === null && isset($_COOKIE['uindex'])) $uindex = $_COOKIE['uindex'];
    if ($hash === null && isset($_COOKIE['hash'])) $hash = $_COOKIE['hash'];
    if ($uindex === null || $hash === null) return false;
    // We don't track hashes now; accept any non-empty value if the user index exists.
    global $db;
    return isset($db->users[(int)$uindex]);
}

// Posts API
function next_post_id() {
    $posts = load_data('posts');
    $max = 0;
    foreach ($posts as $p) { if (!empty($p['id']) && $p['id'] > $max) $max = $p['id']; }
    return $max + 1;
}

function add_post($title, $content, $author = 'anon') {
    $posts = load_data('posts');
    $id = next_post_id();
    $post = [
        'id' => $id,
        'title' => $title,
        'content' => $content,
        'author' => $author,
        'votes' => 0,
        'comments' => [],
        'edits' => [],
        'created_at' => time(),
    ];
    $posts[] = $post;
    save_data('posts', $posts);
    return $id;
}

function addvote($index, $v) {
    $posts = load_data('posts');
    foreach ($posts as &$p) {
        if ((string)$p['id'] === (string)$index) {
            $p['votes'] += intval($v);
            save_data('posts', $posts);
            return true;
        }
    }
    return false;
}

function addcomment($index, $comment) {
    $posts = load_data('posts');
    foreach ($posts as &$p) {
        if ((string)$p['id'] === (string)$index) {
            $p['comments'][] = [
                'author' => $_SESSION['user']['name'] ?? 'anon',
                'content' => $comment,
                'ts' => time()
            ];
            save_data('posts', $posts);
            return true;
        }
    }
    return false;
}

function viewpage($index, $showVotes = true) {
    $posts = load_data('posts');
    foreach ($posts as $p) {
        if ((string)$p['id'] === (string)$index) {
            echo '<div class="view_title">' . htmlspecialchars($p['title']) . '</div>';
            echo '<div class="view_time">' . date('Y-m-d H:i', $p['created_at']) . '</div>';
            echo '<div class="view_view">' . nl2br(htmlspecialchars($p['content'])) . '</div>';
            if ($showVotes) {
                echo '<div class="vote">Votes: ' . intval($p['votes']) . '</div>';
                echo '<form method="post"><button name="voted" value="1">Upvote</button></form>';
            }
            if (!empty($p['comments'])) {
                echo '<h4>Comments</h4>';
                foreach ($p['comments'] as $c) {
                    echo '<div class="com"><span class="com_auth">' . htmlspecialchars($c['author']) . '</span> <span class="com_tstamp">' . date('Y-m-d H:i', $c['ts']) . '</span><div>' . htmlspecialchars($c['content']) . '</div></div>';
                }
            }
            return true;
        }
    }
    printError('Page not found');
    return false;
}

function gettitlefromindex($index) {
    $posts = load_data('posts');
    foreach ($posts as $p) if ((string)$p['id'] === (string)$index) return $p['title'];
    return '';
}

function getcontent_nl2br($index) {
    $posts = load_data('posts');
    foreach ($posts as $p) if ((string)$p['id'] === (string)$index) return htmlspecialchars($p['content']);
    return '';
}

function editpagetext($index, $content) {
    $posts = load_data('posts');
    foreach ($posts as &$p) {
        if ((string)$p['id'] === (string)$index) {
            $p['edits'][] = ['content' => $p['content'], 'ts' => time(), 'editor' => $_SESSION['user']['name'] ?? 'anon'];
            $p['content'] = $content;
            save_data('posts', $posts);
            return true;
        }
    }
    return false;
}

function showedits($index) {
    $posts = load_data('posts');
    foreach ($posts as $p) {
        if ((string)$p['id'] === (string)$index) {
            if (empty($p['edits'])) { echo '<div>No edits</div>'; return; }
            foreach ($p['edits'] as $e) {
                echo '<div class="com"><b>' . htmlspecialchars($e['editor']) . '</b> <span class="tsdiff">' . date('Y-m-d H:i', $e['ts']) . '</span><div>' . nl2br(htmlspecialchars($e['content'])) . '</div></div>';
            }
            return;
        }
    }
    printError('Page not found');
}

function viewprofile($uindex) {
    global $db;
    if (is_numeric($uindex)) {
        $idx = (int)$uindex;
        if (isset($db->users[$idx])) {
            $u = $db->users[$idx];
            echo '<h2>' . htmlspecialchars($u['name']) . '</h2>';
            echo '<div>Joined: ' . date('Y-m-d', $u['created_at']) . '</div>';
            return true;
        }
    } else {
        // lookup by name
        foreach ($db->users as $u) if ($u['name'] === $uindex) {
            echo '<h2>' . htmlspecialchars($u['name']) . '</h2>';
            echo '<div>Joined: ' . date('Y-m-d', $u['created_at']) . '</div>';
            return true;
        }
    }
    printError('User not found');
    return false;
}

// Convenience: handle new post action from index.php router (if used)
if (php_sapi_name() !== 'cli' && basename($_SERVER['SCRIPT_NAME']) === 'index.php') {
    // support a simple action-based flow: ?a=newpost
    if (isset($_GET['a']) && $_GET['a'] === 'newpost' && !empty($_POST['user_title'])) {
        $title = $_POST['user_title'];
        $content = $_POST['user_content'] ?? '';
        $author = $_SESSION['user']['name'] ?? 'anon';
        add_post($title, $content, $author);
        header('Location: ./');
        exit;
    }
}

?>