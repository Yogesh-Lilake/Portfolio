<?php
/**
 * ENTERPRISE LOGGER
 * Safe, concurrent, auto-create folders, rotate logs, JSON support.
 */

if (!defined('APP_LOG_FILE')) {
    die("APP_LOG_FILE not defined. Check paths.php configuration.");
}

if(!function_exists('app_log')) {
    /**
 * Write a log entry.
 *
 * @param string $message
 * @param string $level    info | warning | error | debug
 * @param array  $context  Additional data (assoc array)
 */
function app_log(string $message, string $level = 'info', array $context = []): void
{
    $logDir = dirname(APP_LOG_FILE);

    // Create directory if missing
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    // Rotate log if larger than 5MB
    if (file_exists(APP_LOG_FILE) && filesize(APP_LOG_FILE) > 5 * 1024 * 1024) {
        rename(APP_LOG_FILE, APP_LOG_FILE . '.' . date('Y-m-d_H-i-s') . '.bak');
    }

    $entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'level'     => strtoupper($level),
        'message'   => $message,
        'context'   => $context,
        'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
        'url'       => $_SERVER['REQUEST_URI'] ?? null,
        'user_agent'=> $_SERVER['HTTP_USER_AGENT'] ?? null
    ];

    // Convert to JSON log entry
    $logLine = json_encode($entry) . PHP_EOL;

    // Write atomically with file lock
    $fp = fopen(APP_LOG_FILE, 'a');
    if ($fp) {
        flock($fp, LOCK_EX);
        fwrite($fp, $logLine);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
}
?>
