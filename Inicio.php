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
        body {
            background-color: #f4f6f9;
        }

        .card {
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        h1 {
            font-weight: bold;
        }

        .btn-lg {
            border-radius: 10px;
            font-weight: 500;
        }

        .alert {
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="bg-light">
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

            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4">
                <a href="vistas/verProductoView.php" class="btn btn-success btn-lg px-4">
                    🛒 Ver Productos
                </a>
                <a href="vistas/usuariosView.php" class="btn btn-primary btn-lg px-4">
                    👥 Gestionar Usuarios
                </a>
                <a href="vistas/verCategoria.php" class="btn btn-warning btn-lg px-4">
                    📦 Ver Categorías
                </a>
                <a href="vistas/graficos.php" class="btn btn-info btn-lg px-4">
                    📊 Gráficos
                </a>
                <a href="vistas/graficosMensuales.php" class="btn btn-info btn-lg px-4">
                    📈 Gráficos Mensuales
                </a>
                <a href="logout.php" class="btn btn-danger btn-lg px-4">
                    🚪 Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</body>

</html>