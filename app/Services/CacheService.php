<?php

class CacheService {

    private static string $path = ROOT_PATH . "cache/";
    private static int $defaultTTL = 3600; // 1 hour cache expiry
    private static string $version = "v1"; // bump to invalidate all cache

    /**
     * Sanitizes cache key
     */
    private static function sanitizeKey(string $key): string {
        $key = strtolower(trim($key));
        $key = preg_replace("/[^a-z0-9_\-]/i", "_", $key);
        return self::$version . "_" . $key;
    }

    /**
     * Load cache (with TTL support)
     */
    public static function load(string $key) {
        $key = self::sanitizeKey($key);
        $file = self::$path . $key . ".json";

        if (!file_exists($file)) return false;

        $json = file_get_contents($file);
        if (!$json) return false;

        $data = json_decode($json, true);

        if (!is_array($data)) return false;

        // TTL check
        if (isset($data["_expires"]) && $data["_expires"] < time()) {
            unlink($file);
            return false;
        }

        if (empty($data["payload"]) || !is_array($data["payload"])) {
    return false; // treat empty cache as no cache
}
return $data["payload"];

    }

    /**
     * Save Cache
     */
    public static function save(string $key, $data, int $ttl = null) {
        if (!is_dir(self::$path)) {
            mkdir(self::$path, 0777, true);
        }

        $key = self::sanitizeKey($key);
        $file = self::$path . $key . ".json";

        $ttl = $ttl ?: self::$defaultTTL;

        $payload = [
            "_expires" => time() + $ttl,
            "payload"  => $data
        ];

        file_put_contents($file, json_encode($payload, JSON_PRETTY_PRINT));
    }

    /**
     * Delete a single cached key
     */
    public static function delete(string $key) {
        $key = self::sanitizeKey($key);
        $file = self::$path . $key . ".json";
        if (file_exists($file)) unlink($file);
    }

    /**
     * Clear all cache
     */
    public static function clear() {
        foreach (glob(self::$path . "*.json") as $file) {
            unlink($file);
        }
    }
}
