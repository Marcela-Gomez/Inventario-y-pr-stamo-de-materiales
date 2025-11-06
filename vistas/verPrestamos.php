<?php
session_start();
require_once(__DIR__ . '/../modelo/addMovimiento.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$id_usuario = $_SESSION['usuario']['id'];
echo $id_usuario;
$nombre = htmlspecialchars($usuario['nombre'] ?? '');

$mov = new addMovimiento();
$prestamos = $mov->consulta("
    SELECT 
        m.id_movimiento,
        p.nombre_producto,
        m.cantidad,
        m.fecha_movimiento,
        m.observacion
    FROM movimientos m
    INNER JOIN productos p ON m.id_producto = p.id_producto
    WHERE m.tipo_movimiento = 'Prestamo' and m.estado = 'Activo'
      AND m.id_prestatario = '$id_usuario'
    ORDER BY m.fecha_movimiento DESC
");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .card {
            border-radius: 15px;
            padding: 2rem;
        }

        h1 {
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <h1 class="text-center mb-3">📋 Mis Préstamos</h1>
            <p class="text-center text-muted">Usuario: <strong><?= $nombre ?></strong></p>

            <?php if ($prestamos && $prestamos->num_rows > 0): ?>
                <div class="table-responsive mt-4">
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
                            <?php while ($row = $prestamos->fetch_assoc()): ?>
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
                <div class="alert alert-info text-center mt-4">
                    ⚠️ No tienes préstamos registrados todavía.
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="prestatario.php" class="btn btn-secondary">⬅ Volver al panel</a>
            </div>
        </div>
    </div>
</body>

</html>