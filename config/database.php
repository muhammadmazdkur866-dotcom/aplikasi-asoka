<?php
/**
 * Database Connection Helper (PDO Singleton)
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Tampilan error yang rapi dan informatif
                die("
                    <div style='font-family:sans-serif; max-width:600px; margin:50px auto; padding:25px; border:1px solid #f5c6cb; background:#f8d7da; color:#721c24; border-radius:8px;'>
                        <h3 style='margin-top:0;'>Gagal Terhubung ke Database MySQL!</h3>
                        <p>Pastikan layanan MySQL di XAMPP Control Panel sudah berstatus <strong>Running</strong> dan database <code>" . DB_NAME . "</code> sudah dibuat.</p>
                        <p><small>Detail Error: " . htmlspecialchars($e->getMessage()) . "</small></p>
                    </div>
                ");
            }
        }
        return self::$instance;
    }
}

