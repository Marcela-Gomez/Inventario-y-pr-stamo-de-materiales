<?php
session_start();
require_once(__DIR__ . '/../modelo/addMovimiento.php');
require_once(__DIR__ . '/../modelo/addProducto.php');

// 🔒 Verificar sesión activa y rol comprador
if (!isset($_SESSION['usuario']) || strtolower(trim($_SESSION['usuario']['rol'])) !== 'comprador') {
    header("Location: ../index.php");
    exit;
}

// 🧩 Datos del usuario
$usuario = $_SESSION['usuario'];
$id_usuario = $_SESSION['usuario']['id'];
$nombre = htmlspecialchars($usuario['nombre'] ?? 'Usuario');
$nombreUsuario = htmlspecialchars($usuario['usuario'] ?? 'Desconocido');
$rol = htmlspecialchars(ucfirst($usuario['rol'] ?? 'Sin rol'));

// 🔍 Consultar historial de compras del comprador
$mov = new addMovimiento();
$sql = "
    SELECT 
        m.id_movimiento,
        p.nombre_producto,
        m.cantidad,
        m.observacion,
        m.fecha_movimiento,
        m.tipo_movimiento
    FROM movimientos m
    INNER JOIN productos p ON m.id_producto = p.id_producto
    WHERE m.id_comprador = '$id_usuario'
      AND m.tipo_movimiento IN ('Compra', 'Entrada')
    ORDER BY m.fecha_movimiento DESC
";
$compras = $mov->consulta($sql);
$tiene_compras = ($compras && $compras->num_rows > 0);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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
            background-color: #F8F5F0; /* Fondo Claro */
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #2B2B2B; /* Texto Oscuro */
        }

        .card {
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            margin: 0 auto;
        }

        h2 {
            font-weight: 700;
            color: #8B0000; /* Vino Principal */
        }

        .table th {
            background-color: #9B001F; /* Rojo Ladrillo */
            color: #F8F5F0; /* Fondo Claro */
        }

        .table td .badge {
            background-color: #B38C00; /* Dorado/Ocre para cantidades */
            color: #2B2B2B;
        }

        .btn-volver {
            background-color: #6F4E37; /* Café Suave */
            color: #F8F5F0;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }

        .btn-volver:hover {
            background-color: #5a3d2d;
            color: #F8F5F0;
        }

        .badge-compra {
            background-color: #8B0000; /* Vino Principal */
            color: #F8F5F0;
        }

        .badge-prestamo {
            background-color: #3b82f6; /* Azul para préstamos (opcional) */
            color: #F8F5F0;
        }

        .empty {
            font-size: 1.1rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h2>🧾 Historial de Compras</h2>
                <p class="text-muted mb-1">
                    Bienvenido <strong><?= $nombre ?></strong> (<?= $nombreUsuario ?>)
                </p>
                <span class="badge badge-compra"><?= $rol ?></span>
            </div>

            <?php if ($tiene_compras): ?>
                <div class="table-responsive">
                    <table class="table table-hover text-center align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Descripción / Observación</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $compras->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_movimiento']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
                                    <td><span class="badge"><?= htmlspecialchars($row['cantidad']) ?></span></td>
                                    <td><?= htmlspecialchars($row['observacion'] ?: '—') ?></td>
                                    <td>
                                        <span class="badge <?= $row['tipo_movimiento'] === 'Compra' ? 'badge-compra' : 'badge-prestamo' ?>">
                                            <?= htmlspecialchars($row['tipo_movimiento']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['fecha_movimiento'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-emoji-neutral fs-1 text-muted"></i>
                    <p class="empty mt-2">Aún no has realizado ninguna compra.</p>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="comprador.php" class="btn btn-volver">
                    ⬅️ Volver al Panel del Comprador
                </a>
            </div>
        </div>
    </div>
</body>

</html>
