<?php $tituloPagina = htmlspecialchars($nombre) . ' — MeteoApp'; ?>
<?php include __DIR__ . '/layout_base.php'; ?>

<div class="container pb-5" style="max-width:760px;">

    <?php
    $p = http_build_query(['lat'=>$lat,'lon'=>$lon,'nombre'=>$nombre,'pais'=>$pais]);
    $opciones = [
        ['accion'=>'actual',  'icono'=>'bi-thermometer-half', 'color'=>'#388bfd',
         'titulo'=>'Tiempo actual', 'desc'=>'Temperatura, viento, humedad y presion ahora mismo.'],
        ['accion'=>'horas',   'icono'=>'bi-clock',            'color'=>'#3fb950',
         'titulo'=>'Prevision por horas', 'desc'=>'Como evoluciona el tiempo en las proximas 24 horas.'],
        ['accion'=>'semana',  'icono'=>'bi-calendar3',        'color'=>'#d2991d',
         'titulo'=>'Prevision semanal', 'desc'=>'Resumen del tiempo para los proximos 5 dias.'],
    ];
    ?>

    <div class="row g-3">
    <?php foreach ($opciones as $op): ?>
        <div class="col-md-4">
            <a href="index.php?accion=<?= $op['accion'] ?>&<?= $p ?>" class="text-decoration-none d-block h-100">
                <div class="caja caja-cuerpo h-100"
                     onmouseover="this.style.borderColor='<?= $op['color'] ?>'"
                     onmouseout="this.style.borderColor='#30363d'"
                     style="transition:border-color .2s; cursor:pointer;">
                    <i class="bi <?= $op['icono'] ?> fs-2 mb-3 d-block" style="color:<?= $op['color'] ?>;"></i>
                    <div class="text-white fw-bold mb-2"><?= $op['titulo'] ?></div>
                    <p class="text-secondary mb-0" style="font-size:.84rem;"><?= $op['desc'] ?></p>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
    </div>

</div>
</body>
</html>
