<?php
session_start();
require_once('../modelo/addMovimiento.php');
require_once('../modelo/addProducto.php');

// 🔒 Verificar sesión activa y rol "prestamista"
if (
    !isset($_SESSION['usuario']) ||
    !isset($_SESSION['usuario']['rol']) ||
    strtolower(trim($_SESSION['usuario']['rol'])) !== 'prestamista'
) {
    header("Location: ../index.php");
    exit;
}

// 🧩 Datos del usuario
$usuario = array_map('htmlspecialchars', $_SESSION['usuario']);
$nombre = $usuario['nombre'] ?? 'Usuario';
$nombreUsuario = $usuario['usuario'] ?? 'Desconocido';
$rol = $usuario['rol'] ?? 'Sin rol';
$id_usuario = $_SESSION['usuario']['id'];

    // 📦 Modelos
    $mov = new addMovimiento();
$productoModel = new addProducto();
$cn = new cn();
$mensaje = '';

/* --------------------------------------------------------
   ✅ Registrar devolución de préstamo
-------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_movimiento'])) {
    $id_movimiento = (int) $_POST['id_movimiento'];
    $id_producto = (int) $_POST['id_producto'];
    $cantidad = (int) $_POST['cantidad'];
    $id_prestatario = $id_usuario;

    // 🧾 Registrar movimiento tipo "Devolucion"
    $resultado = $mov->registrarMovimiento([
        'id_producto' => $id_producto,
        'cantidad' => $cantidad,
        'tipo_movimiento' => 'Devolucion',
        'observacion' => 'Devolución de préstamo registrada por el prestamista',
        'id_prestamista' => $id_usuario
    ]);

    if (isset($resultado['success'])) {
        
        // ✅ Cambiar estado del préstamo original
        $cn->consulta("UPDATE movimientos SET estado='Devuelto' WHERE id_movimiento='$id_movimiento'");

        $mensaje = "<div class='alert alert-success text-center'>✅ Devolución registrada correctamente.</div>";

    } else {
        $mensaje = "<div class='alert alert-danger text-center'>❌ Error al registrar devolución: " . htmlspecialchars($resultado['error']) . "</div>";
    }
}

/* --------------------------------------------------------
   🔍 Consultar préstamos activos
-------------------------------------------------------- */
$prestamos = $mov->consulta("
    SELECT 
        m.id_movimiento, 
        m.id_producto,
        u.nombre AS prestatario, 
        u.id_usuario AS id_prestatario,
        p.nombre_producto, 
        m.cantidad, 
        m.observacion, 
        m.fecha_movimiento
    FROM movimientos m
    INNER JOIN productos p ON m.id_producto = p.id_producto
    INNER JOIN usuarios u ON m.id_prestatario = u.id_usuario
    WHERE m.tipo_movimiento = 'Prestamo' and m.estado = 'Activo'
    ORDER BY m.fecha_movimiento DESC
");



/* --------------------------------------------------------
   🔍 Consultar historial de devoluciones
-------------------------------------------------------- */
$devoluciones = $mov->consulta("
    

SELECT 
        m.id_movimiento, 
        m.id_producto,
        u.nombre AS prestatario, 
        u.id_usuario AS id_prestatario,
        p.nombre_producto, 
        m.cantidad, 
        m.observacion, 
        m.fecha_movimiento
    FROM movimientos m
    INNER JOIN productos p ON m.id_producto = p.id_producto
    INNER JOIN usuarios u ON m.id_prestatario = u.id_usuario
    WHERE m.tipo_movimiento = 'Prestamo' and m.estado = 'Devuelto'
    ORDER BY m.fecha_movimiento DESC
");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Prestamista - Gestión de Préstamos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
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

        table {
            background-color: white;
        }

        th {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>

<body class="bg-light">

        <!-- 🧍 Bienvenida -->
        <div class="card shadow-lg p-4 text-center mb-5">
            <h1>👋 Bienvenido, <?= $nombre ?></h1>
            <p class="lead mb-2">Has iniciado sesión como <strong><?= $nombreUsuario ?></strong></p>
            <p class="text-muted">Rol: <?= ucfirst($rol) ?></p>
            <hr>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-4">
                <a href="../logout.php" class="btn btn-danger btn-lg px-4">🚪 Cerrar Sesión</a>
            </div>
        </div>

        <!-- 📋 Mensaje -->
        <?= $mensaje ?>

        <!-- 🤝 Préstamos activos -->
        <div class="card shadow-lg p-4 mb-4">
            <h3 class="mb-3 text-center">🤝 Préstamos Activos</h3>
                                <!-- 🔍 Barra de búsqueda -->
                    <div class="mb-3 text-end">
                        <input 
                            type="text" 
                            id="filtroPrestamos" 
                            class="form-control w-50 d-inline" 
                            placeholder="🔍 Buscar por nombre, producto o fecha..." 
                            onkeyup="filtrarPrestamos()">
                    </div>

            <?php if ($prestamos && $prestamos->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>Prestatario</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Observación</th>
                                <th>Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $prestamos->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['prestatario']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
                                    <td><?= htmlspecialchars($row['cantidad']) ?></td>
                                    <td><?= htmlspecialchars($row['observacion']) ?></td>
                                    <td><?= htmlspecialchars($row['fecha_movimiento']) ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id_movimiento" value="<?= $row['id_movimiento'] ?>">
                                            <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">
                                            <input type="hidden" name="cantidad" value="<?= $row['cantidad'] ?>">
                                            <input type="hidden" name="id_prestatario" value="<?= $row['id_prestatario'] ?>">
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                ↩ Registrar Devolución
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    🕓 No hay préstamos activos actualmente.
                </div>
            <?php endif; ?>
        </div>


        <!-- 📦 Historial de Devoluciones -->
        <div class="card shadow-lg p-4">
            <h3 class="mb-3 text-center">📦 Historial de Devoluciones</h3>
                
                            <!-- 🔍 Barra de búsqueda -->
                <div class="mb-3 text-end">
                    <input 
                        type="text" 
                        id="filtroDevoluciones" 
                        class="form-control w-50 d-inline" 
                        placeholder="🔍 Buscar por prestatario, producto o fecha..." 
                        onkeyup="filtrarDevoluciones()">
                </div>


            <?php if ($devoluciones && $devoluciones->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>Prestatario</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Observación</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $devoluciones->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['prestatario']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
                                    <td><?= htmlspecialchars($row['cantidad']) ?></td>
                                    <td><?= htmlspecialchars($row['observacion']) ?></td>
                                    <td><?= htmlspecialchars($row['fecha_movimiento']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    🕓 No se han registrado devoluciones aún.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
function filtrarPrestamos() {
    const input = document.getElementById("filtroPrestamos");
    const filtro = input.value.toLowerCase();
    const tabla = document.querySelector(".table-responsive table");
    const filas = tabla.getElementsByTagName("tr");

    for (let i = 1; i < filas.length; i++) { // empieza desde 1 para saltar encabezado
        const celdas = filas[i].getElementsByTagName("td");
        let coincide = false;

        // Revisa las columnas de prestatario, producto y fecha
        for (let j of [0, 1, 4]) { // índices: 0=prestatario, 1=producto, 4=fecha
            const texto = celdas[j]?.textContent?.toLowerCase() || "";
            if (texto.includes(filtro)) {
                coincide = true;
                break;
            }
        }

        filas[i].style.display = coincide ? "" : "none";
    }
}


function filtrarDevoluciones() {
    const input = document.getElementById("filtroDevoluciones");
    const filtro = input.value.toLowerCase();
    const tabla = document.querySelectorAll(".table-responsive table")[1]; // segunda tabla (devoluciones)
    const filas = tabla.getElementsByTagName("tr");

    for (let i = 1; i < filas.length; i++) { // saltar encabezado
        const celdas = filas[i].getElementsByTagName("td");
        let coincide = false;

        // Columnas: 0=prestatario, 1=producto, 4=fecha
        for (let j of [0, 1, 4]) {
            const texto = celdas[j]?.textContent?.toLowerCase() || "";
            if (texto.includes(filtro)) {
                coincide = true;
                break;
            }
        }

        filas[i].style.display = coincide ? "" : "none";
    }
}

</script>

</body>

</html>
