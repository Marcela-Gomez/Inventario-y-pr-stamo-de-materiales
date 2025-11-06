<?php
session_start();
require_once(__DIR__ . '/../modelo/addMovimiento.php');
require_once(__DIR__ . '/../modelo/addProducto.php');

// 🔒 Verificar sesión activa y rol "comprador"
if (
    !isset($_SESSION['usuario']) ||
    !isset($_SESSION['usuario']['rol']) ||
    strtolower(trim($_SESSION['usuario']['rol'])) !== 'comprador'
) {
    header("Location: ../index.php");
    exit;
}

// 🧩 Datos del usuario
$usuario = array_map('htmlspecialchars', $_SESSION['usuario']);
$id_usuario = $_SESSION['usuario']['id'];
$nombre = $usuario['nombre'] ?? 'Usuario';
$nombreUsuario = $usuario['usuario'] ?? 'Desconocido';
$rol = ucfirst($usuario['rol'] ?? 'Sin rol');

// 📦 Modelos
$mov = new addMovimiento();

// 🔍 Consultar historial de compras del comprador actual
$compras = $mov->consulta("
    SELECT 
        m.id_movimiento,
        p.nombre_producto,
        m.cantidad,
        m.observacion,
        m.fecha_movimiento,
        m.tipo_movimiento
    FROM movimientos m
    INNER JOIN productos p ON m.id_producto = p.id_producto
    WHERE m.tipo_movimiento IN ('Compra', 'Entrada')
      AND m.id_comprador = '$id_usuario'
    ORDER BY m.fecha_movimiento DESC
");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel del Comprador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #eef2f7;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        h1 {
            font-weight: bold;
            color: #2c3e50;
        }

        .btn-lg {
            border-radius: 10px;
            font-weight: 500;
        }

        table th {
            background-color: #0d6efd;
            color: white;
        }

        .btn-verde {
            background-color: #28a745;
            color: white;
        }

        .btn-verde:hover {
            background-color: #218838;
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

        .btn-azul {
            background-color: #0d6efd;
            color: white;
        }

        .btn-azul:hover {
            background-color: #0b5ed7;
            color: white;
        }

        hr {
            border: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <h1 class="text-center mb-3">👤 Panel del Comprador</h1>
            <p class="text-center text-muted">
                Bienvenido <strong><?= $nombre ?></strong> (<em><?= $nombreUsuario ?></em>) — Rol: <?= $rol ?>
            </p>

            <!-- 🔘 Botones de acciones -->
            <div class="d-flex justify-content-center flex-wrap gap-3 mb-4">
                <a href="registrarCompra.php" class="btn btn-verde btn-lg">🛒 Registrar Nueva Compra</a>
                <a href="compradorProducto.php" class="btn btn-azul btn-lg">📦 Ver Productos</a>
                <a href="../logout.php" class="btn btn-gris btn-lg">🚪 Cerrar Sesión</a>
            </div>

            <hr>

            <h4 class="text-center mb-3">📜 Últimas Compras</h4>

            <?php if ($compras && $compras->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Descripción</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $contador = 0; ?>
                            <?php while ($row = $compras->fetch_assoc()): ?>
                                <?php if ($contador++ >= 5)
                                    break; // solo mostrar las 5 más recientes ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_movimiento']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
                                    <td><span class="badge bg-success"><?= htmlspecialchars($row['cantidad']) ?></span></td>
                                    <td><?= htmlspecialchars($row['observacion'] ?: '-') ?></td>
                                    <td>
                                        <span
                                            class="badge <?= $row['tipo_movimiento'] === 'Compra' ? 'bg-success' : 'bg-primary' ?>">
                                            <?= htmlspecialchars($row['tipo_movimiento']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['fecha_movimiento'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="historialCompras.php" class="btn btn-outline-primary">📄 Ver todo el historial</a>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center mt-4">
                    🕓 No has registrado compras todavía.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>