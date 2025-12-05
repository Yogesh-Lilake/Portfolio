<?php
/**
 * ENTERPRISE DATABASE CONNECTION
 * PDO + Singleton + Auto-reconnect + Error handling
 */

require_once ROOT_PATH . 'config/config.php';
require_once LOGGER_FILE;

// Prevent direct access or loading before config.php
if (!defined('DB_HOST')) {
    die("❌ ERROR: db_connection.php was loaded BEFORE config.php.
         Always load config.php, never load db_connection.php directly.");
}

class DB
{
    private static ?DB $instance = null;
    private ?PDO $pdo = null;

    private function __construct()
    {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT         => false,       // safer for InfinityFree
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } 
        catch (PDOException $e) {

            app_log("DB INITIAL CONNECTION FAILED", "error", [
                "dsn"    => $dsn,
                "error"  => $e->getMessage(),
            ]);

            die("Database connection failed.");
        }
    }

    /**
     * Singleton accessor
     */
    public static function getInstance(): DB
    {
        if (!self::$instance) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection (auto-reconnect if lost)
     */
    public function pdo(): PDO
    {
        try {
            $this->pdo->query("SELECT 1");
        } 
        catch (Throwable $e) {

            app_log("DB AUTO-RECONNECT TRIGGERED", "warning", [
                "error" => $e->getMessage()
            ]);

            self::$instance = new DB();
        }

        return self::$instance->pdo;
    }
}

// GLOBAL PDO SHORTCUT
$db = DB::getInstance()->pdo();

?>
