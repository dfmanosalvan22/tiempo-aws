<?php
require_once __DIR__ . '/../config.php';

class Conexion {

    private static ?PDO $instancia = null;

    private function __construct() {}

    public static function obtener(): PDO {
        if (self::$instancia === null) {
            $dsn = "mysql:host=" . BD_HOST . ";dbname=" . BD_NOMBRE . ";charset=" . BD_CHARSET;
            self::$instancia = new PDO($dsn, BD_USUARIO, BD_PASSWORD, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$instancia;
    }
}
