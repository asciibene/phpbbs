<?php
require __DIR__ . '/scripts/funcdefs.php';

$posts = list_posts();

?><html>
<head>
<title>PHPBBg</title>
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<h1>Welcome to <a class="nlk" href="/docs/about.html">phpBBS</a> !</h1>
<div class="menu">
  <a href="/login.php">Login</a>
  <a href="/register.php">Register</a>
  <a href="/new.php">New post</a>
  <a href="/docs/help.html">Help</a>
</div>
<div>
  <h2>Latest posts</h2>
  <?php if (empty($posts)): ?>
    <div>No posts yet. <a href="/new.php">Create the first post</a>.</div>
  <?php else: ?>
    <ul>
    <?php foreach ($posts as $post): ?>
      <li>
        <a class="link" href="/view.php?index=<?php echo e($post['id']); ?>"><?php echo e($post['title'] ?? '(untitled)'); ?></a>
        <small class="view_time">by <?php echo e($post['author'] ?? 'anon'); ?> — <?php echo date('Y-m-d H:i', (int) ($post['created_at'] ?? 0)); ?></small>
        <span class="view_view"> — votes: <?php echo (int) ($post['votes'] ?? 0); ?></span>
      </li>
    <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
</body>
</html>
