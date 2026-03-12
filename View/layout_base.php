<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina ?? 'MeteoApp') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background:#0d1117; color:#c9d1d9; min-height:100vh; }

        .barra-nav {
            background:#161b22;
            border-bottom:1px solid #30363d;
            padding:.8rem 0;
        }
        .logo { font-weight:800; font-size:1.15rem; color:#fff; text-decoration:none; }
        .logo span { color:#388bfd; }

        .ciudad-activa {
            background:#1f2937; border:1px solid #30363d;
            border-radius:20px; padding:.3rem .9rem;
            font-size:.85rem;
        }
        .nav-vistas .nav-link {
            color:#8b949e; font-size:.88rem;
            border-radius:6px; padding:.4rem .85rem;
        }
        .nav-vistas .nav-link:hover { background:#1f2937; color:#c9d1d9; }
        .nav-vistas .nav-link.activo { background:#1f2937; color:#388bfd; font-weight:600; }

        .caja { background:#161b22; border:1px solid #30363d; border-radius:12px; }
        .caja-cuerpo { padding:1.4rem; }

        .dato-etiqueta { color:#8b949e; font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; }
        .dato-valor { color:#e6edf3; font-size:1.05rem; font-weight:600; }

        .temp-principal { font-size:3.8rem; font-weight:800; color:#fff; line-height:1; }
        .temp-unidad { font-size:1.8rem; color:#8b949e; font-weight:400; }

        .tabla-datos { color:#c9d1d9; --bs-table-bg:transparent; }
        .tabla-datos thead th {
            color:#8b949e; font-size:.72rem; text-transform:uppercase;
            letter-spacing:.06em; border-color:#30363d; font-weight:500; background:#161b22;
        }
        .tabla-datos td { border-color:#30363d; vertical-align:middle; background:#161b22; color:#c9d1d9; }
        .tabla-datos tbody tr:hover td { background:#1c2128; }

        .aviso-error {
            background:rgba(248,81,73,.08); border:1px solid rgba(248,81,73,.3);
            border-radius:10px; color:#ff7b72;
        }
        .pildora { font-size:.72rem; padding:.2rem .6rem; border-radius:20px; font-weight:500; }
        .pildora-actual  { background:rgba(56,139,253,.15); color:#388bfd; }
        .pildora-horas   { background:rgba(63,185,80,.15);  color:#3fb950; }
        .pildora-semana  { background:rgba(210,153,34,.15); color:#d2991d; }
    </style>
</head>
<body>

<nav class="barra-nav mb-4">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">

        <a href="index.php" class="logo">Felipe<span>App</span></a>

        <?php if (!empty($nombre)):
            $p = http_build_query(['lat'=>$lat,'lon'=>$lon,'nombre'=>$nombre,'pais'=>$pais]);
            $accionActual = $_GET['accion'] ?? '';
        ?>
        <div class="ciudad-activa">
            <i class="bi bi-geo-alt-fill me-1" style="color:#388bfd;"></i>
            <?= htmlspecialchars($nombre) ?>, <?= htmlspecialchars($pais) ?>
        </div>

        <ul class="nav nav-vistas mb-0">
            <li class="nav-item">
                <a class="nav-link <?= $accionActual==='actual' ?'activo':'' ?>"
                   href="index.php?accion=actual&<?= $p ?>">
                    <i class="bi bi-thermometer-half me-1"></i>Actual
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $accionActual==='horas' ?'activo':'' ?>"
                   href="index.php?accion=horas&<?= $p ?>">
                    <i class="bi bi-clock me-1"></i>Por horas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $accionActual==='semana' ?'activo':'' ?>"
                   href="index.php?accion=semana&<?= $p ?>">
                    <i class="bi bi-calendar3 me-1"></i>Semana
                </a>
            </li>
        </ul>
        <?php endif; ?>

        <a href="index.php?accion=historial" class="nav-link text-secondary" style="font-size:.85rem;">
            <i class="bi bi-clock-history"></i>
        </a>

    </div>
</nav>
