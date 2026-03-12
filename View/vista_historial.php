<?php
$tituloPagina = 'Historial — MeteoApp';
$nombre = null; // Sin ciudad activa, el layout no muestra la barra de navegacion
include __DIR__ . '/layout_base.php';
?>

<div class="container pb-5" style="max-width:900px;">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h5 class="text-white mb-0">
            <i class="bi bi-clock-history me-2" style="color:#388bfd;"></i>Historial de consultas
        </h5>
        <a href="index.php" class="text-secondary text-decoration-none" style="font-size:.85rem;">
            <i class="bi bi-search me-1"></i>Nueva busqueda
        </a>
    </div>

    <!-- Ranking -->
    <?php if (!empty($ranking)): ?>
    <div class="caja caja-cuerpo mb-4">
        <div class="dato-etiqueta mb-3">Ciudades mas consultadas</div>
        <div class="d-flex flex-wrap gap-2">
        <?php foreach ($ranking as $i => $r): ?>
            <span style="background:#1c2128;border:1px solid #30363d;border-radius:20px;padding:.35rem .9rem;font-size:.85rem;">
                <span class="text-secondary"><?= $i+1 ?>.</span>
                <span class="text-white ms-1"><?= htmlspecialchars($r['ciudad']) ?></span>
                <span class="text-secondary">, <?= htmlspecialchars($r['pais']) ?></span>
                <span style="color:#388bfd;" class="ms-1"><?= $r['total'] ?></span>
            </span>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabla -->
    <?php if (empty($consultas)): ?>
        <div class="text-secondary text-center py-5">
            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
            Todavia no hay consultas registradas.
        </div>
    <?php else: ?>
    <div class="caja">
        <div class="table-responsive">
            <table class="table tabla-datos mb-0">
                <thead>
                    <tr><th>Ciudad</th><th>Tipo</th><th>Fecha</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($consultas as $c):
                    $p = http_build_query([
                        'lat'=>$c['latitud'], 'lon'=>$c['longitud'],
                        'nombre'=>$c['ciudad'], 'pais'=>$c['pais']
                    ]);
                ?>
                    <tr>
                        <td>
                            <span style="color:#e6edf3;"><?= htmlspecialchars($c['ciudad']) ?></span>
                            <span class="text-secondary">, <?= htmlspecialchars($c['pais']) ?></span>
                        </td>
                        <td>
                            <span class="pildora pildora-<?= $c['tipo_consulta'] ?>">
                                <?= $c['tipo_consulta'] ?>
                            </span>
                        </td>
                        <td class="text-secondary" style="font-size:.85rem;"><?= $c['fecha_consulta'] ?></td>
                        <td>
                            <a href="index.php?accion=<?= $c['tipo_consulta'] ?>&<?= $p ?>"
                               style="color:#388bfd;font-size:.82rem;text-decoration:none;">
                                Repetir
                            </a>
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
