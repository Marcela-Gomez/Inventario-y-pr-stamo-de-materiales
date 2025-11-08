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
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #2B2B2B;
    }

    .card {
        border-radius: 15px;
        padding: 2rem;
        border-top: 5px solid #8B0000; /* Toque institucional */
        background-color: #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    h1 {
        color: #8B0000;
        font-weight: bold;
    }

    p {
        color: #2B2B2B;
    }

    table {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #6F4E37;
    }

    th {
        background-color: #8B0000; /* Vino Principal */
        color: #F8F5F0;
        text-transform: uppercase;
    }

    .btn-secondary {
        background-color: #6F4E37; /* Café Suave */
        color: #fff;
        border: 1px solid #8B0000;
        transition: 0.2s;
    }

    .btn-secondary:hover {
        background-color: #9B001F; /* Rojo Ladrillo */
        border-color: #8B0000;
        color: #fff;
    }

    .alert {
        background-color: #F8F5F0;
        border-color: #B38C00;
        color: #2B2B2B;
        font-size: 0.95rem;
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