<?php
session_start();
require_once(__DIR__ . '/../modelo/addMovimiento.php');

// ✅ Evitar acceso sin sesión activa
if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

// ✅ Sanitizar los datos de sesión
$usuario = array_map('htmlspecialchars', $_SESSION['usuario']);

// ✅ Asignar variables con valores seguros
$nombre = $usuario['nombre'] ?? 'Usuario';
$nombreUsuario = $usuario['usuario'] ?? 'Desconocido';
$rol = ucfirst($usuario['rol'] ?? 'Sin rol');
$id_usuario = $_SESSION['usuario']['id'];

// ✅ Mensaje si faltan datos
if (empty($nombreUsuario) || empty($rol)) {
    $mensajeAdvertencia = "⚠️ Algunos datos de la sesión no se encontraron correctamente.";
}

// 📦 Consultar historial de devoluciones del prestatario
$mov = new addMovimiento();
$devoluciones = $mov->consulta("
    SELECT 
        m.id_movimiento, 
        p.nombre_producto, 
        m.cantidad, 
        m.observacion, 
        m.fecha_movimiento
    FROM movimientos m
    INNER JOIN productos p ON m.id_producto = p.id_producto
    WHERE m.tipo_movimiento = 'Devolucion'
    ORDER BY m.fecha_movimiento DESC
");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel del Prestatario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* ============================================================
       🎨 PALETA INSTITUCIONAL ITCA-FEPADE
       ------------------------------------------------------------
       - Vino Principal:        #8B0000
       - Rojo Ladrillo:         #9B001F
       - Dorado/Ocre:           #B38C00
       - Café Suave:            #6F4E37
       - Fondo Claro:           #F8F5F0
       - Texto Oscuro:          #2B2B2B
    ============================================================ */

    body {
        background-color: #F8F5F0;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        color: #2B2B2B;
    }

    .card {
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        padding: 2rem;
        border-top: 5px solid #8B0000; /* Toque institucional */
    }

    h1, h4 {
        font-weight: bold;
        color: #8B0000;
    }

    p, td {
        color: #2B2B2B;
    }

    .btn-lg {
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-verde {
        background-color: #B38C00; /* Dorado/Ocre */
        color: #fff;
        border-color: #6F4E37;
    }

    .btn-verde:hover {
        background-color: #6F4E37; /* Café Suave */
        border-color: #B38C00;
        color: #fff;
    }

    .btn-primary {
        background-color: #8B0000; /* Vino Principal */
        border-color: #9B001F; /* Rojo Ladrillo */
    }

    .btn-primary:hover {
        background-color: #9B001F;
        border-color: #8B0000;
    }

    .btn-gris {
        background-color: #6F4E37; /* Café Suave */
        color: #fff;
    }

    .btn-gris:hover {
        background-color: #5a3f2d;
        color: #fff;
    }

    table {
        background-color: #fff;
        border: 1px solid #6F4E37;
    }

    th {
        background-color: #8B0000;
        color: #F8F5F0;
        text-transform: uppercase;
    }

    input.form-control {
        border: 1px solid #B38C00;
        border-radius: 8px;
    }

    input.form-control:focus {
        box-shadow: 0 0 5px #B38C00;
        border-color: #B38C00;
    }

    .alert-info, .alert-warning {
        background-color: #F8F5F0;
        border-color: #B38C00;
        color: #2B2B2B;
    }

    .alert-warning {
        border-color: #9B001F;
    }
</style>

</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <h1 class="text-center mb-3">📦 Panel del Prestatario</h1>
            <p class="text-center text-muted">
                Bienvenido <strong><?= $nombre ?></strong> (<?= $nombreUsuario ?>) — Rol: <?= $rol ?>
            </p>

            <?php if (!empty($mensajeAdvertencia)): ?>
                <div class="alert alert-warning text-center mt-3">
                    <?= $mensajeAdvertencia ?>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-center gap-3 mb-4 mt-4">
                <a href="crearPrestamo.php" class="btn btn-verde btn-lg">➕ Crear Prestamo</a>
                <a href="registrarSalida.php" class="btn btn-verde btn-lg">➕ Crear Salida</a>
                <a href="verPrestamos.php" class="btn btn-primary btn-lg">📋 Ver Préstamos Activos</a>
                <a href="../logout.php" class="btn btn-gris btn-lg">🚪 Cerrar Sesión</a>
            </div>

            <hr>

            <h4 class="text-center mb-3">📜 Historial de Devoluciones</h4>

            <?php if ($devoluciones && $devoluciones->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Observación</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $devoluciones->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_movimiento']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
                                    <td><?= htmlspecialchars($row['cantidad']) ?></td>
                                    <td><?= htmlspecialchars($row['observacion'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($row['fecha_movimiento']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    ⚠️ No hay devoluciones registradas todavía.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>