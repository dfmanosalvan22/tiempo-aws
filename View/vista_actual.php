<?php $tituloPagina = 'Actual — ' . htmlspecialchars($nombre); ?>
<?php include __DIR__ . '/layout_base.php'; ?>

<div class="container pb-5" style="max-width:860px;">

<?php if (empty($datos)): ?>
    <div class="aviso-error p-3">
        <i class="bi bi-exclamation-circle me-2"></i>No se pudieron obtener los datos. Comprueba la API key.
    </div>
<?php else: ?>

    <div class="row g-3">

        <!-- Temperatura -->
        <div class="col-md-5">
            <div class="caja caja-cuerpo h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="https://openweathermap.org/img/wn/<?= $datos['weather'][0]['icon'] ?>@2x.png"
                         alt="" style="width:64px;">
                    <div>
                        <div class="temp-principal">
                            <?= round($datos['main']['temp']) ?><span class="temp-unidad">°C</span>
                        </div>
                        <div class="text-secondary" style="font-size:.9rem;">
                            <?= htmlspecialchars(ucfirst($datos['weather'][0]['description'])) ?>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-4 pt-3" style="border-top:1px solid #30363d;">
                    <div>
                        <div class="dato-etiqueta">Sensacion</div>
                        <div class="dato-valor"><?= round($datos['main']['feels_like']) ?>°C</div>
                    </div>
                    <div>
                        <div class="dato-etiqueta">Minima</div>
                        <div class="dato-valor"><?= round($datos['main']['temp_min']) ?>°C</div>
                    </div>
                    <div>
                        <div class="dato-etiqueta">Maxima</div>
                        <div class="dato-valor"><?= round($datos['main']['temp_max']) ?>°C</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles -->
        <div class="col-md-7">
            <div class="caja caja-cuerpo h-100">
                <div class="row g-2">
                <?php
                $detalles = [
                    ['bi-droplet',      'Humedad',   $datos['main']['humidity'] . ' %'],
                    ['bi-speedometer2', 'Presion',   $datos['main']['pressure'] . ' hPa'],
                    ['bi-wind',         'Viento',    $datos['wind']['speed'] . ' m/s'],
                    ['bi-clouds',       'Nubosidad', $datos['clouds']['all'] . ' %'],
                    ['bi-sunrise',      'Amanecer',  date('H:i', $datos['sys']['sunrise'])],
                    ['bi-sunset',       'Atardecer', date('H:i', $datos['sys']['sunset'])],
                ];
                foreach ($detalles as [$icono, $etiqueta, $valor]):
                ?>
                <div class="col-6 col-sm-4">
                    <div style="background:#0d1117;border-radius:8px;padding:.85rem;">
                        <i class="bi <?= $icono ?> mb-1 d-block" style="color:#388bfd;"></i>
                        <div class="dato-etiqueta"><?= $etiqueta ?></div>
                        <div class="dato-valor"><?= $valor ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

    <p class="text-secondary mt-3" style="font-size:.78rem;">
        <i class="bi bi-arrow-clockwise me-1"></i>Datos obtenidos: <?= date('d/m/Y H:i') ?>
    </p>

<?php endif; ?>
</div>
</body>
</html>
