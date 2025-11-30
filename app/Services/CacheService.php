<?php

class CacheService {

    /**
     * CACHE DIRECTORY PATH (Defined in paths.php)
     * Absolute path example:
     * /home/yourname/htdocs/project/cache/
     */
    private static string $path = CACHE_PATH;

    private static int $defaultTTL = 3600; // 1 hour
    private static string $version = "v1"; // bump to clear all cache instantly

    /**
     * Ensure cache folder exists
     */
    private static function ensureDir()
    {
        if (!is_dir(self::$path)) {
            mkdir(self::$path, 0755, true); // 0755 safe for InfinityFree
        }
    }

    /**
     * Sanitize key
     * Converts key to safe filesystem key
     */
    private static function sanitizeKey(string $key): string
    {
        $key = strtolower(trim($key));
        $key = preg_replace("/[^a-z0-9_\-]/i", "_", $key);
        return self::$version . "_" . $key;
    }

    /**
     * Load cached data
     * Returns false on missing/expired/invalid cache
     */
    public static function load(string $key)
    {
        self::ensureDir();

        $key = self::sanitizeKey($key);
        $file = self::$path . $key . ".json";

        if (!file_exists($file)) return false;

        $json = @file_get_contents($file);
        if (!$json) return false;

        $data = json_decode($json, true);

        if (!is_array($data)) return false;

        // TTL check
        if (isset($data["_expires"]) && $data["_expires"] < time()) {
            unlink($file);
            return false;
        }

        // Ensure payload exists
        if (empty($data["payload"]) || !is_array($data["payload"])) {
            return false;
        }

        return $data["payload"];
    }

    /**
     * Save cache
     */
    public static function save(string $key, $data, int $ttl = null)
    {
        self::ensureDir();

        $key = self::sanitizeKey($key);
        $file = self::$path . $key . ".json";

        $ttl = $ttl ?: self::$defaultTTL;

        $payload = [
            "_expires" => time() + $ttl,
            "payload"  => $data,
            "_saved_at" => date("Y-m-d H:i:s")
        ];

        file_put_contents($file, json_encode($payload, JSON_PRETTY_PRINT));
    }

    /**
     * Delete a single cached key
     */
    public static function delete(string $key)
    {
        self::ensureDir();

        $key = self::sanitizeKey($key);
        $file = self::$path . $key . ".json";

        if (file_exists($file)) unlink($file);
    }

    /**
     * Clear ALL cache
     */
    public static function clear()
    {
        self::ensureDir();

        foreach (glob(self::$path . "*.json") as $file) {
            unlink($file);
        }
    }
}
