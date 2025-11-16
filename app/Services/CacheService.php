<?php
class CacheService {

    private static $path = ROOT_PATH . "cache/";

    public static function load($key) {
        $file = self::$path . $key . ".json";
        if (!file_exists($file)) return false;
        return json_decode(file_get_contents($file), true);
    }

    public static function save($key, $data) {
        if (!is_dir(self::$path)) mkdir(self::$path, 0777, true);
        $file = self::$path . $key . ".json";
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }
}
