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
            color: #8B0000;
        }

        .btn-lg {
            border-radius: 10px;
            font-weight: 500;
        }

        table th {
            background-color: #8B0000;
            color: white;
        }

        .btn-verde {
            background-color: #B38C00;
            color: white;
        }

        .btn-verde:hover {
            background-color: #9B001F;
            color: white;
        }

        .btn-gris {
            background-color: #6F4E37;
            color: white;
        }

        .btn-gris:hover {
            background-color: #5a3d2e;
            color: white;
        }

        .btn-azul {
            background-color: #8B0000;
            color: white;
        }

        .btn-azul:hover {
            background-color: #9B001F;
            color: white;
        }

        hr {
            border: 1px solid #B38C00;
        }

        .text-muted {
            color: #6F4E37 !important;
        }

        .badge.bg-success {
            background-color: #B38C00 !important;
        }

        .badge.bg-primary {
            background-color: #8B0000 !important;
        }

        .btn-outline-primary {
            border-color: #8B0000;
            color: #8B0000;
        }

        .btn-outline-primary:hover {
            background-color: #8B0000;
            color: white;
        }
    </style>
</head>

<body>
   
<!-- ✅ Menú de Navegación -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <!-- Título o logo -->
        <a class="navbar-brand fw-bold" href="comprador.php">
            🛒 Compras
        </a>


        <!-- Items del menú -->
        <div class="collapse navbar-collapse" id="menuCompras">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a href="registrarCompra.php" class="btn btn-success btn-lg mx-2 px-4">
                        🛒 Registrar Nueva Compra
                    </a>
                </li>

                <li class="nav-item">
                    <a href="compradorProducto.php" class="btn btn-primary btn-lg mx-2 px-4">
                        📦 Ver Productos
                    </a>
                </li>

                <li class="nav-item">
                    <a href="../logout.php" class="btn btn-danger btn-lg mx-2 px-4">
                        🚪 Cerrar Sesión
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>



    <div class="container mt-5">
        <div class="card">
            <h1 class="text-center mb-3">👤 Panel del Comprador</h1>
            <p class="text-center text-muted">
                Bienvenido <strong><?= $nombre ?></strong> (<em><?= $nombreUsuario ?></em>) — Rol: <?= $rol ?>
            </p>

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
                                    break; ?>
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
