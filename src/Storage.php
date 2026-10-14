<?php
class Storage {
    public static function path($name) {
        $dir = __DIR__ . '/../data';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir . '/' . $name . '.json';
    }

    public static function ensure($name) {
        $file = self::path($name);
        if (!file_exists($file)) {
            file_put_contents($file, "[]");
        }
        return $file;
    }

    public static function read($name) {
        $file = self::ensure($name);
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    public static function write($name, $data) {
        $file = self::ensure($name);
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
