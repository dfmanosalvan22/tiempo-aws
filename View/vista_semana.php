<?php $tituloPagina = 'Semana — ' . htmlspecialchars($nombre); ?>
<?php include __DIR__ . '/layout_base.php'; ?>

<div class="container pb-5" style="max-width:900px;">

<?php if (empty($datos)): ?>
    <div class="aviso-error p-3">
        <i class="bi bi-exclamation-circle me-2"></i>No se pudieron obtener los datos semanales.
    </div>
<?php else:
    $diasSemana = ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'];

    // Preparamos datos para la grafica
    $etiquetas = [];
    $temps     = [];
    foreach ($datos as $fecha => $r) {
        $etiquetas[] = $diasSemana[date('w', strtotime($fecha))];
        $temps[]     = round($r['main']['temp']);
    }

    $config = json_encode([
        'type' => 'bar',
        'data' => [
            'labels'   => $etiquetas,
            'datasets' => [[
                'label'           => 'Temperatura (°C)',
                'data'            => $temps,
                'backgroundColor' => 'rgba(56,139,253,0.6)',
                'borderColor'     => '#388bfd',
                'borderWidth'     => 1,
                'borderRadius'    => 6,
            ]]
        ],
        'options' => [
            'plugins' => ['legend' => ['labels' => ['color' => '#c9d1d9']]],
            'scales'  => [
                'x' => ['ticks' => ['color' => '#8b949e'], 'grid' => ['color' => '#30363d']],
                'y' => ['ticks' => ['color' => '#8b949e'], 'grid' => ['color' => '#30363d']],
            ]
        ]
    ]);
    $urlGrafica = 'https://quickchart.io/chart?backgroundColor=%230d1117&c=' . urlencode($config) . '&width=800&height=280';
?>

    <!-- Grafica -->
    <div class="caja caja-cuerpo mb-3">
        <div class="dato-etiqueta mb-3">Temperatura por dia</div>
        <img src="<?= htmlspecialchars($urlGrafica) ?>" alt="Grafica semanal"
             style="width:100%;border-radius:8px;">
    </div>

    <!-- Tabla -->
    <div class="caja">
        <div class="table-responsive">
            <table class="table tabla-datos mb-0">
                <thead>
                    <tr>
                        <th>Dia</th>
                        <th></th>
                        <th>Estado</th>
                        <th>Temp.</th>
                        <th>Humedad</th>
                        <th>Viento</th>
                        <th>Lluvia</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($datos as $fecha => $r):
                    $ts     = strtotime($fecha);
                    $lluvia = $r['rain']['3h'] ?? $r['rain']['1h'] ?? 0;
                ?>
                    <tr>
                        <td>
                            <div style="color:#e6edf3;font-weight:600;"><?= $diasSemana[date('w',$ts)] ?></div>
                            <div class="text-secondary" style="font-size:.8rem;"><?= date('d/m', $ts) ?></div>
                        </td>
                        <td>
                            <img src="https://openweathermap.org/img/wn/<?= $r['weather'][0]['icon'] ?>.png"
                                 alt="" style="width:36px;">
                        </td>
                        <td><?= htmlspecialchars(ucfirst($r['weather'][0]['description'])) ?></td>
                        <td style="color:#e6edf3;font-weight:600;"><?= round($r['main']['temp']) ?>°C</td>
                        <td><i class="bi bi-droplet text-primary me-1"></i><?= $r['main']['humidity'] ?> %</td>
                        <td><i class="bi bi-wind text-secondary me-1"></i><?= $r['wind']['speed'] ?> m/s</td>
                        <td>
                            <?php if ($lluvia > 0): ?>
                                <span style="color:#388bfd;"><i class="bi bi-cloud-rain me-1"></i><?= $lluvia ?> mm</span>
                            <?php else: ?>
                                <span class="text-secondary">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>
</div>
</body>
</html>
