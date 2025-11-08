<?php
session_start();
require_once(__DIR__ . '/../modelo/addMovimiento.php');
require_once(__DIR__ . '/../modelo/addProducto.php');

// 🔒 Verificar sesión y rol "prestamista"
if (
    !isset($_SESSION['usuario']) ||
    !isset($_SESSION['usuario']['rol']) ||
    strtolower(trim($_SESSION['usuario']['rol'])) !== 'prestamista'
) {
    header("Location: ../index.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$id_usuario = $usuario['id'];
$nombre = htmlspecialchars($usuario['nombre']);
$nombreUsuario = htmlspecialchars($usuario['usuario']);
$rol = htmlspecialchars($usuario['rol']);

// 📦 Modelos
$mov = new addMovimiento();
$productoModel = new addProducto();
$productos = $productoModel->getProductos();

$mensaje = "";

// ✅ Procesar formulario de devolución
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = (int) $_POST['id_producto'];
    $cantidad = (int) $_POST['cantidad'];
    $observacion = htmlspecialchars($_POST['observacion']);

    if ($id_producto > 0 && $cantidad > 0) {
        // Crear arreglo de datos para el método registrarMovimiento
        $datos = [
            'id_producto' => $id_producto,
            'cantidad' => $cantidad,
            'tipo_movimiento' => 'Devolucion',
            'observacion' => $observacion,
            'id_usuario' => $id_usuario
        ];

        $resultado = $mov->registrarMovimiento($datos);

        if (isset($resultado['error'])) {
            $mensaje = '<div class="alert alert-danger text-center">' . htmlspecialchars($resultado['error']) . '</div>';
        } else {
            $mensaje = '<div class="alert alert-success text-center">✅ Devolución registrada correctamente.</div>';
        }
    } else {
        $mensaje = '<div class="alert alert-warning text-center">⚠️ Debes seleccionar un producto y una cantidad válida.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Devolución</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
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
        max-width: 600px;
        margin: 50px auto;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        padding: 2rem;
        border-top: 5px solid #8B0000; /* Toque institucional */
        background-color: #fff;
    }

    h3 {
        font-weight: bold;
        color: #8B0000;
    }

    p {
        color: #2B2B2B;
    }

    .btn-guardar {
        background-color: #B38C00; /* Dorado/Ocre */
        color: #fff;
        font-weight: 500;
        transition: 0.2s;
        border: 1px solid #6F4E37;
    }

    .btn-guardar:hover {
        background-color: #6F4E37; /* Café Suave */
        border-color: #B38C00;
        color: #fff;
    }

    .btn-volver {
        background-color: #6F4E37; /* Café Suave */
        color: #fff;
        transition: 0.2s;
        border: 1px solid #8B0000;
    }

    .btn-volver:hover {
        background-color: #9B001F; /* Rojo Ladrillo */
        border-color: #8B0000;
        color: #fff;
    }

    select.form-select, input.form-control, textarea.form-control {
        border: 1px solid #B38C00;
        border-radius: 8px;
    }

    select.form-select:focus,
    input.form-control:focus,
    textarea.form-control:focus {
        box-shadow: 0 0 5px #B38C00;
        border-color: #B38C00;
    }
</style>


</head>

<body>
    <div class="container">
        <div class="card p-4">
            <h3 class="text-center mb-4">🔁 Registrar Devolución</h3>

            <p class="text-center text-muted mb-4">
                Bienvenido <strong><?= $nombre ?></strong> (<?= $nombreUsuario ?>) — Rol: <?= ucfirst($rol) ?>
            </p>

            <?= $mensaje ?>

            <form method="POST">
                <!-- Producto -->
                <div class="mb-3">
                    <label class="form-label">Selecciona un producto:</label>
                    <select name="id_producto" class="form-select" required>
                        <option value="">-- Elige un producto --</option>
                        <?php while ($row = mysqli_fetch_assoc($productos)): ?>
                            <option value="<?= $row['id_producto'] ?>">
                                <?= htmlspecialchars($row['nombre_producto']) ?> (Stock: <?= $row['stock'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Cantidad -->
                <div class="mb-3">
                    <label class="form-label">Cantidad a devolver:</label>
                    <input type="number" name="cantidad" class="form-control" min="1" required>
                </div>

                <!-- Observación -->
                <div class="mb-3">
                    <label class="form-label">Observación (opcional):</label>
                    <textarea name="observacion" class="form-control" rows="3"
                        placeholder="Notas sobre la devolución..."></textarea>
                </div>

                <!-- Botones -->
                <div class="text-center">
                    <button type="submit" class="btn btn-guardar px-4">💾 Registrar</button>
                    <a href="prestamista.php" class="btn btn-volver px-4">⬅️ Volver</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>