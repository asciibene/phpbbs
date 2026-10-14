<?php
require_once __DIR__ . '/Storage.php';

class Forum {
    public static function listPosts() {
        $posts = Storage::read('posts');
        usort($posts, function ($a, $b) {
            return ((int) ($b['created_at'] ?? 0)) <=> ((int) ($a['created_at'] ?? 0));
        });
        return $posts;
    }

    public static function nextPostId() {
        $posts = Storage::read('posts');
        $max = 0;
        foreach ($posts as $post) {
            $max = max($max, (int) ($post['id'] ?? 0));
        }
        return $max + 1;
    }

    public static function createPost($title, $content, $author) {
        $title = trim((string) $title);
        $content = trim((string) $content);
        $author = trim((string) $author);

        if ($title === '' || $content === '') {
            return false;
        }

        $posts = Storage::read('posts');
        $post = [
            'id' => self::nextPostId(),
            'title' => $title,
            'content' => $content,
            'author' => $author !== '' ? $author : 'anon',
            'votes' => 0,
            'comments' => [],
            'edits' => [],
            'created_at' => time(),
        ];

        $posts[] = $post;
        Storage::write('posts', $posts);
        return $post['id'];
    }

    public static function findPostById($id) {
        foreach (self::listPosts() as $post) {
            if ((string) $post['id'] === (string) $id) {
                return $post;
            }
        }
        return null;
    }

    public static function addVote($id, $delta) {
        $posts = Storage::read('posts');
        foreach ($posts as &$post) {
            if ((string) $post['id'] === (string) $id) {
                $post['votes'] = (int) ($post['votes'] ?? 0) + (int) $delta;
                Storage::write('posts', $posts);
                return true;
            }
        }
        return false;
    }

    public static function addComment($id, $comment, $author) {
        $comment = trim((string) $comment);
        if ($comment === '') {
            return false;
        }

        $posts = Storage::read('posts');
        foreach ($posts as &$post) {
            if ((string) $post['id'] === (string) $id) {
                $post['comments'][] = [
                    'author' => $author ?: 'anon',
                    'content' => $comment,
                    'ts' => time(),
                ];
                Storage::write('posts', $posts);
                return true;
            }
        }
        return false;
    }

    public static function editPost($id, $content, $editor) {
        $content = trim((string) $content);
        if ($content === '') {
            return false;
        }

        $posts = Storage::read('posts');
        foreach ($posts as &$post) {
            if ((string) $post['id'] === (string) $id) {
                $post['edits'][] = [
                    'content' => $post['content'],
                    'editor' => $editor ?: 'anon',
                    'ts' => time(),
                ];
                $post['content'] = $content;
                Storage::write('posts', $posts);
                return true;
            }
        }
        return false;
    }
}
