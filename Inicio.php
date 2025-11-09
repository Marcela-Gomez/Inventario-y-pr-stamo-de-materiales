<?php
session_start();

// ✅ Evitar acceso sin sesión activa
if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// ✅ Sanitizar los datos de sesión para evitar inyecciones XSS
$usuario = array_map('htmlspecialchars', $_SESSION['usuario']);

// ✅ Asignar variables con valores seguros
$nombre = $usuario['nombre'] ?? 'Usuario';
$nombreUsuario = $usuario['usuario'] ?? 'Desconocido';
$rol = $usuario['rol'] ?? 'Sin rol';

// ✅ Mensaje de advertencia si faltan datos en la sesión
if (empty($nombreUsuario) || empty($rol)) {
    $mensajeAdvertencia = "⚠️ Algunos datos de la sesión no se encontraron correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Inicio - Inventario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    
    <style>
    /* ============================================================
       🎨 PALETA ITCA-FEPADE
       ------------------------------------------------------------
       - Vino Principal:  #8B0000
       - Rojo Ladrillo:   #9B001F
       - Dorado/Ocre:     #B38C00
       - Café Suave:      #6F4E37
       - Fondo Claro:     #F8F5F0
       - Texto Oscuro:    #2B2B2B
    ============================================================ */

    body {
        background-color: #F8F5F0;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    .card {
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        padding: 2rem;
    }

    h1 {
        font-weight: bold;
        color: #8B0000; /* Vino Principal */
    }

    p.lead, p.text-muted {
        color: #2B2B2B;
    }

    .alert-warning {
        background-color: #FFF4E5;
        border: 1px solid #B38C00; /* Dorado/Ocre */
        color: #2B2B2B;
        font-size: 0.95rem;
    }

    .btn-lg {
        border-radius: 10px;
        font-weight: 500;
        transition: 0.2s;
    }

    .btn-success {
        background-color: #198754; /* Verde institucional */
        border: none;
    }
    .btn-success:hover {
        background-color: #157347;
    }

    .btn-primary {
        background-color: #8B0000; /* Vino Principal */
        border: none;
    }
    .btn-primary:hover {
        background-color: #9B001F; /* Rojo Ladrillo */
    }

    .btn-warning {
        background-color: #B38C00; /* Dorado/Ocre */
        border: none;
    }
    .btn-warning:hover {
        background-color: #9B7B00;
    }

    .btn-info {
        background-color: #6F4E37; /* Café Suave */
        border: none;
        color: white;
    }
    .btn-info:hover {
        background-color: #593A2B;
    }

    .btn-danger {
        background-color: #9B001F; /* Rojo Ladrillo */
        border: none;
    }
    .btn-danger:hover {
        background-color: #8B0000;
    }
</style>


</head>

<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="inicio.php">
            🛍 Panel Principal
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="btn btn-success me-2 mb-2" href="vistas/verProductoView.php">
                        🛒 Ver Productos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-primary me-2 mb-2" href="vistas/usuariosView.php">
                        👥 Gestionar Usuarios
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-warning me-2 mb-2" href="vistas/verCategoria.php">
                        📦 Ver Categorías
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-info me-2 mb-2" href="vistas/graficos.php">
                        📊 Gráficos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-info me-2 mb-2" href="vistas/graficosMensuales.php">
                        📈 Gráficos Mensuales
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-danger me-2 mb-2" href="logout.php">
                        🚪 Cerrar Sesión
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>


    <div class="container mt-5">
        <div class="card shadow-lg p-4 text-center">
            <h1>👋 Bienvenido, <?= $nombre ?></h1>
            <p class="lead mb-2">
                Has iniciado sesión como <strong><?= $nombreUsuario ?></strong>
            </p>
            <p class="text-muted">Rol: <?= $rol ?></p>

            <?php if (!empty($mensajeAdvertencia)): ?>
                <div class="alert alert-warning text-center mt-3">
                    <?= $mensajeAdvertencia ?>
                </div>
            <?php endif; ?>

            <hr>
                
            
        </div>
    </div>
</body>

</html>