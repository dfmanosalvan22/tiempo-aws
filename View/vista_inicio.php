<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeteoApp</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background:#0d1117; color:#c9d1d9;
            min-height:100vh; display:flex;
            align-items:center; justify-content:center;
        }
        .caja-busqueda {
            background:#161b22; border:1px solid #30363d;
            border-radius:16px; padding:2.5rem;
            width:100%; max-width:460px;
        }
        .logo { font-weight:800; font-size:2.4rem; letter-spacing:-1px; color:#fff; }
        .logo span { color:#388bfd; }
        .campo {
            background:#0d1117; border:1px solid #30363d;
            color:#e6edf3; border-radius:8px;
            padding:.7rem 1rem; font-size:.95rem;
            width:100%; transition:border-color .2s;
        }
        .campo:focus {
            outline:none; border-color:#388bfd;
            box-shadow:0 0 0 3px rgba(56,139,253,.15);
            background:#0d1117; color:#e6edf3;
        }
        .campo::placeholder { color:#484f58; }
        .btn-buscar {
            background:#238636; border:none; border-radius:8px;
            padding:.7rem 1.2rem; color:#fff; font-weight:600;
        }
        .btn-buscar:hover { background:#2ea043; }
        .aviso-error {
            background:rgba(248,81,73,.08); border:1px solid rgba(248,81,73,.3);
            border-radius:8px; color:#ff7b72; font-size:.88rem;
        }
    </style>
</head>
<body>
<div class="caja-busqueda">
    <div class="logo">Meteo<span>App</span></div>
    <p class="text-secondary mb-4" style="font-size:.88rem;">Consulta el tiempo en cualquier ciudad del mundo</p>

    <?php if (!empty($error)): ?>
        <div class="aviso-error p-3 mb-3">
            <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="index.php" method="get">
        <input type="hidden" name="accion" value="buscar">
        <div class="d-flex gap-2">
            <input type="text" name="ciudad" class="campo"
                   placeholder="Madrid, Tokyo, New York..."
                   required autofocus
                   value="<?= htmlspecialchars($_GET['ciudad'] ?? '') ?>">
            <button type="submit" class="btn-buscar">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    <div class="text-center mt-4">
        <a href="index.php?accion=historial" class="text-secondary text-decoration-none" style="font-size:.82rem;">
            <i class="bi bi-clock-history me-1"></i>Historial de consultas
        </a>
    </div>
</div>
</body>
</html>
