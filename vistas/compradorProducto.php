<?php
session_start();
require_once('../modelo/addProducto.php');

// 🔒 Verificar sesión activa y rol comprador
if (!isset($_SESSION['usuario']) || strtolower($_SESSION['usuario']['rol']) !== 'comprador') {
    header("Location: ../index.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$nombre = htmlspecialchars($usuario['nombre']);
$nombreUsuario = htmlspecialchars($usuario['usuario']);
$rol = htmlspecialchars($usuario['rol']);

// 📦 Modelo
$productoModel = new addProducto();
$productos = $productoModel->getProductos();
$tiene_productos = ($productos && mysqli_num_rows($productos) > 0);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Catálogo de Productos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* ============================================================
           🎨 PALETA ITCA-FEPADE
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

        .container {
            max-width: 1100px;
        }

        .card {
            border: none;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        table th,
        table td {
            vertical-align: middle !important;
        }

        h2 {
            font-weight: 700;
            color: #8B0000;
        }

        .btn-volver {
            background-color: #6F4E37;
            color: white;
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-volver:hover {
            background-color: #5a3d2e;
            color: white;
        }

        .table thead {
            background-color: #8B0000;
            color: #fff;
        }

        .table tbody tr:hover {
            background-color: #F8F5F0;
        }

        .badge {
            font-size: 0.9rem;
        }

        .badge.bg-success {
            background-color: #B38C00 !important;
        }

        .badge.bg-warning.text-dark {
            background-color: #9B001F !important;
            color: white !important;
        }

        .badge.bg-secondary {
            background-color: #6F4E37 !important;
        }

        .text-muted, .text-secondary {
            color: #6F4E37 !important;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h2>🛍️ Catálogo de Productos</h2>
                <p class="text-muted mb-0">
                    Bienvenido <strong><?= $nombre ?></strong> (<?= $nombreUsuario ?>)
                </p>
                <p class="text-secondary">Rol: <?= ucfirst($rol) ?></p>
            </div>

            <?php if ($tiene_productos): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Categoría</th>
                                <th>Tipo</th>
                                <th>Devolvible</th>
                                <th>Stock</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($productos)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_producto']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
                                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_categoria'] ?? 'Sin categoría') ?></td>
                                    <td><?= htmlspecialchars($row['tipo_producto']) ?></td>
                                    <td><?= $row['puede_devolverse'] ? 'Sí' : 'No' ?></td>
                                    <td><?= htmlspecialchars($row['stock']) ?></td>
                                    <td>
                                        <?= $row['fecha_vencimiento'] !== '0000-00-00'
                                            ? htmlspecialchars($row['fecha_vencimiento'])
                                            : '-' ?>
                                    </td>
                                    <td>
                                        <?php if ($row['estado'] === 'Disponible'): ?>
                                            <span class="badge bg-success">Disponible</span>
                                        <?php elseif ($row['estado'] === 'Agotado'): ?>
                                            <span class="badge bg-warning text-dark">Agotado</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Descontinuado</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center mt-4">
                    ⚠️ No hay productos registrados todavía.
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="../vistas/comprador.php" class="btn btn-volver">⬅️ Volver al Inicio</a>
            </div>
        </div>
    </div>
</body>

</html>
