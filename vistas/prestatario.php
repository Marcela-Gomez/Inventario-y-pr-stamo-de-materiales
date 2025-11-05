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
        body {
            background-color: #f4f6f9;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            padding: 2rem;
        }

        h1 {
            font-weight: bold;
            color: #333;
        }

        .btn-lg {
            border-radius: 10px;
            font-weight: 500;
        }

        table th {
            background-color: #0d6efd;
            color: white;
        }

        .alert {
            font-size: 0.9rem;
        }

        .btn-verde {
            background-color: #198754;
            color: white;
        }

        .btn-verde:hover {
            background-color: #157347;
            color: white;
        }

        .btn-gris {
            background-color: #6c757d;
            color: white;
        }

        .btn-gris:hover {
            background-color: #5a6268;
            color: white;
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