<?php
require_once __DIR__ . '/../DB/Conexion.php';

class ConsultaDAO {

    private PDO $bd;

    public function __construct() {
        $this->bd = Conexion::obtener();
    }

    public function guardar(string $ciudad, string $pais, float $lat, float $lon, string $tipo): void {
        $stmt = $this->bd->prepare(
            "INSERT INTO consultas (ciudad, pais, latitud, longitud, tipo_consulta)
             VALUES (:ciudad, :pais, :lat, :lon, :tipo)"
        );
        $stmt->execute([':ciudad'=>$ciudad, ':pais'=>$pais, ':lat'=>$lat, ':lon'=>$lon, ':tipo'=>$tipo]);
    }

    public function obtenerHistorial(): array {
        return $this->bd->query("SELECT * FROM consultas ORDER BY fecha_consulta DESC")->fetchAll();
    }

    public function obtenerRanking(): array {
        return $this->bd->query(
            "SELECT ciudad, pais, COUNT(*) AS total FROM consultas
             GROUP BY ciudad, pais ORDER BY total DESC LIMIT 5"
        )->fetchAll();
    }
}
