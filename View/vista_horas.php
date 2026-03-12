<?php $tituloPagina = 'Por horas — ' . htmlspecialchars($nombre); ?>
<?php include __DIR__ . '/layout_base.php'; ?>

<div class="container pb-5" style="max-width:900px;">

<?php if (empty($datos)): ?>
    <div class="aviso-error p-3">
        <i class="bi bi-exclamation-circle me-2"></i>No hay datos disponibles.
    </div>
<?php else: ?>

    <?php
    // Preparamos los arrays de etiquetas y temperaturas para la grafica
    $horas = [];
    $temps = [];
    foreach ($datos as $r) {
        $horas[] = date('H:i', $r['dt']);
        $temps[] = round($r['main']['temp']);
    }

    // Construimos la URL de QuickChart con la configuracion del grafico
    // QuickChart recibe un JSON con la config y devuelve una imagen PNG
    $config = json_encode([
        'type' => 'line',
        'data' => [
            'labels'   => $horas,
            'datasets' => [[
                'label'           => 'Temperatura (°C)',
                'data'            => $temps,
                'borderColor'     => '#388bfd',
                'backgroundColor' => 'rgba(56,139,253,0.15)',
                'fill'            => true,
                'tension'         => 0.4,
                'pointRadius'     => 5,
                'pointBackgroundColor' => '#388bfd',
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
        <div class="dato-etiqueta mb-3">Temperatura proximas horas</div>
        <img src="<?= htmlspecialchars($urlGrafica) ?>" alt="Grafica de temperatura"
             style="width:100%;border-radius:8px;">
    </div>

    <!-- Tabla -->
    <div class="caja">
        <div class="table-responsive">
            <table class="table tabla-datos mb-0">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th></th>
                        <th>Estado</th>
                        <th>Temp.</th>
                        <th>Sensacion</th>
                        <th>Humedad</th>
                        <th>Viento</th>
                        <th>Nubes</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($datos as $r): ?>
                    <tr>
                        <td style="color:#e6edf3;font-weight:600;"><?= date('H:i', $r['dt']) ?></td>
                        <td>
                            <img src="https://openweathermap.org/img/wn/<?= $r['weather'][0]['icon'] ?>.png"
                                 alt="" style="width:36px;">
                        </td>
                        <td><?= htmlspecialchars(ucfirst($r['weather'][0]['description'])) ?></td>
                        <td style="color:#e6edf3;font-weight:600;"><?= round($r['main']['temp']) ?>°C</td>
                        <td><?= round($r['main']['feels_like']) ?>°C</td>
                        <td><i class="bi bi-droplet text-primary me-1"></i><?= $r['main']['humidity'] ?> %</td>
                        <td><i class="bi bi-wind text-secondary me-1"></i><?= $r['wind']['speed'] ?> m/s</td>
                        <td><?= $r['clouds']['all'] ?> %</td>
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
