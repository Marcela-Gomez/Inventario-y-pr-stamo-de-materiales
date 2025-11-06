<?php
session_start();
require_once(__DIR__ . '/../modelo/addMovimiento.php');
require_once(__DIR__ . '/../modelo/addProducto.php');

// ✅ Verificar sesión activa
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('⚠️ No has iniciado sesión.'); window.location.href='../index.php';</script>";
    exit;
}

$usuario = $_SESSION['usuario'];
$id_usuario = $_SESSION['usuario']['id'];
$rol = strtolower(trim($usuario['rol'] ?? ''));

// ✅ Validar que el rol sea comprador (acepta mayúsculas o minúsculas)
if ($rol !== 'comprador') {
    echo "<script>alert('❌ Acceso denegado. Solo los compradores pueden registrar compras.'); window.location.href='../index.php';</script>";
    exit;
}

$modeloProducto = new addProducto();
$modeloMovimiento = new addMovimiento();

// ✅ Verificar que el usuario existe en la base de datos
$checkUsuario = $modeloMovimiento->consulta("SELECT id_usuario FROM usuarios WHERE id_usuario = '$id_usuario' LIMIT 1");
if (!$checkUsuario || $checkUsuario->num_rows === 0) {
    echo "<script>alert('⚠️ El usuario con ID $id_usuario no existe en la base de datos.'); window.location.href='../index.php';</script>";
    exit;
}

// ✅ Obtener productos disponibles
$productos = $modeloProducto->getProductos();

// 📦 Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = (int) ($_POST['id_producto'] ?? 0);
    $cantidad = (int) ($_POST['cantidad'] ?? 0);
    $observacion = trim($_POST['observacion'] ?? '');

    if ($id_producto <= 0 || $cantidad <= 0) {
        echo "<script>alert('⚠️ Debes seleccionar un producto y una cantidad válida.');</script>";
    } else {
        // 🧾 Datos del movimiento
        $datos = [
            'id_producto' => $id_producto,
            'cantidad' => $cantidad,
            'tipo_movimiento' => 'Compra',
            'observacion' => $observacion,
            'id_usuario' => $id_usuario
        ];

        $resultado = $modeloMovimiento->registrarMovimiento($datos);

        if (isset($resultado['error'])) {
            echo "<script>alert('❌ {$resultado['error']}');</script>";
        } else {
            echo "<script>alert('✅ Compra registrada correctamente.'); window.location.href='comprador.php';</script>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Compra</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f6fa;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            max-width: 600px;
            margin: 50px auto;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        h2 {
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
        }

        .btn-volver {
            background-color: #6c757d;
            color: white;
        }

        .btn-volver:hover {
            background-color: #5a6268;
            color: white;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>🛒 Registrar Nueva Compra</h2>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="id_producto" class="form-label">Producto:</label>
                <select name="id_producto" id="id_producto" class="form-select" required>
                    <option value="">Seleccione un producto</option>
                    <?php while ($row = mysqli_fetch_assoc($productos)): ?>
                        <option value="<?= $row['id_producto'] ?>">
                            <?= htmlspecialchars($row['nombre_producto']) ?> (Stock actual: <?= $row['stock'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad comprada:</label>
                <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
            </div>

            <div class="mb-3">
                <label for="observacion" class="form-label">Descripción / Observación:</label>
                <textarea class="form-control" id="observacion" name="observacion" rows="3"
                    placeholder="Ejemplo: Compra de reposición de stock."></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="comprador.php" class="btn btn-volver">⬅️ Volver</a>
                <button type="submit" class="btn btn-primary">Registrar Compra ✅</button>
            </div>
        </form>
    </div>
</body>

</html>