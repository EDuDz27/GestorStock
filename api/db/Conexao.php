<?php
namespace db;

use PDO;
use PDOException;

class Conexao {
    private static $host = 'localhost';
    private static $dbname = 'gestor_stock';
    private static $pdo;
    private static $dbuser = 'root';
    private static $dbpass = '';

    public static function conectar() {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8mb4", self::$dbuser, self::$dbpass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro de conexão: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>
