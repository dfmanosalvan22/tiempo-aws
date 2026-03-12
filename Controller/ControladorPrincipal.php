<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/ConsultaDAO.php';

class ControladorPrincipal {

    private ConsultaDAO $dao;

    public function __construct() {
        $this->dao = new ConsultaDAO();
    }

    public function ejecutar(): void {
        $accion = $_GET['accion'] ?? 'inicio';

        switch ($accion) {
            case 'buscar':    $this->buscarCiudad();    break;
            case 'actual':    $this->mostrarActual();   break;
            case 'horas':     $this->mostrarHoras();    break;
            case 'semana':    $this->mostrarSemana();   break;
            case 'historial': $this->mostrarHistorial();break;
            default: require __DIR__ . '/../View/vista_inicio.php';
        }
    }

    private function buscarCiudad(): void {
        $ciudad = trim($_GET['ciudad'] ?? '');

        if (empty($ciudad)) {
            $error = "Escribe el nombre de una ciudad.";
            require __DIR__ . '/../View/vista_inicio.php';
            return;
        }

        $url  = API_BASE . "/geo/1.0/direct?q=" . urlencode($ciudad) . "&limit=1&appid=" . API_KEY;
        $data = $this->api($url);

        if (empty($data)) {
            $error = "No se encontro \"" . htmlspecialchars($ciudad) . "\".";
            require __DIR__ . '/../View/vista_inicio.php';
            return;
        }

        $lat    = $data[0]['lat'];
        $lon    = $data[0]['lon'];
        $nombre = $data[0]['name'];
        $pais   = $data[0]['country'];

        require __DIR__ . '/../View/vista_opciones.php';
    }

    private function mostrarActual(): void {
        [$lat, $lon, $nombre, $pais] = $this->params();
        if ($lat === null) return;

        $datos = $this->api(API_BASE . "/data/2.5/weather?lat=$lat&lon=$lon&units=metric&lang=es&appid=" . API_KEY);

        $this->dao->guardar($nombre, $pais, $lat, $lon, 'actual');
        require __DIR__ . '/../View/vista_actual.php';
    }

    private function mostrarHoras(): void {
        [$lat, $lon, $nombre, $pais] = $this->params();
        if ($lat === null) return;

        $respuesta = $this->api(API_BASE . "/data/2.5/forecast?lat=$lat&lon=$lon&units=metric&lang=es&appid=" . API_KEY);

        // Cogemos las proximas 8 franjas (cada 3 horas = 24 horas vista)
        $datos = array_slice($respuesta['list'] ?? [], 0, 8);

        $this->dao->guardar($nombre, $pais, $lat, $lon, 'horas');
        require __DIR__ . '/../View/vista_horas.php';
    }

    private function mostrarSemana(): void {
        [$lat, $lon, $nombre, $pais] = $this->params();
        if ($lat === null) return;

        $respuesta = $this->api(API_BASE . "/data/2.5/forecast?lat=$lat&lon=$lon&units=metric&lang=es&appid=" . API_KEY);

        // Un registro por dia: preferimos el de las 12:00
        $datos = [];
        foreach ($respuesta['list'] ?? [] as $item) {
            $dia  = substr($item['dt_txt'], 0, 10);
            $hora = substr($item['dt_txt'], 11, 5);
            if (!isset($datos[$dia]) || $hora === '12:00') {
                $datos[$dia] = $item;
            }
        }

        $this->dao->guardar($nombre, $pais, $lat, $lon, 'semana');
        require __DIR__ . '/../View/vista_semana.php';
    }

    private function mostrarHistorial(): void {
        $consultas = $this->dao->obtenerHistorial();
        $ranking   = $this->dao->obtenerRanking();
        require __DIR__ . '/../View/vista_historial.php';
    }

    // Llama a la API con cURL y devuelve el array con los datos
    private function api(string $url): array {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $json = curl_exec($ch);
        curl_close($ch);

        $datos = json_decode($json, true);
        if (!is_array($datos)) return [];
        if (isset($datos['cod']) && (int)$datos['cod'] !== 200) return [];

        return $datos;
    }

    // Extrae lat, lon, nombre y pais de la URL
    private function params(): array {
        $lat    = $_GET['lat']    ?? null;
        $lon    = $_GET['lon']    ?? null;
        $nombre = $_GET['nombre'] ?? null;
        $pais   = $_GET['pais']   ?? '';

        if ($lat === null || $lon === null || $nombre === null) {
            $error = "Faltan datos. Haz una nueva busqueda.";
            require __DIR__ . '/../View/vista_inicio.php';
            return [null, null, null, null];
        }
        return [(float)$lat, (float)$lon, $nombre, $pais];
    }

}
