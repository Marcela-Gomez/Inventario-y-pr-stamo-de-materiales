<?php
session_start();
require_once(__DIR__ . '/../modelo/addMovimiento.php');
require_once(__DIR__ . '/../cn.php');

// 🔒 Verificar sesión activa
if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

$usuario = $_SESSION['usuario'];

// 🧩 Obtener ID de usuario de forma segura
$id_usuario = $_SESSION['usuario']['id'];
echo $id_usuario;
if ($id_usuario <= 0) {
    echo "<script>alert('⚠️ Error: No se encontró el ID del usuario. Vuelve a iniciar sesión.'); window.location.href='../index.php';</script>";
    exit;
}

$nombre = htmlspecialchars($usuario['nombre'] ?? 'Usuario');
$nombreUsuario = htmlspecialchars($usuario['usuario'] ?? 'Desconocido');
$rol = htmlspecialchars(ucfirst($usuario['rol'] ?? 'Sin rol'));

// 📦 Conexión y productos disponibles
$cn = new cn();
$productos = $cn->consulta("SELECT id_producto, nombre_producto, stock FROM productos WHERE estado = 'Disponible' and stock > 0 and puede_devolverse = 0");

$mensaje = "";

// ✅ Procesar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movimiento = new addMovimiento();

    $datos = [
        'id_producto' => $_POST['id_producto'] ?? 0,
        'cantidad' => $_POST['cantidad'] ?? 0,
        'tipo_movimiento' => 'Salida',
        'observacion' => $_POST['observacion'] ?? '',
        'id_prestatario' => $id_usuario
    ];

    $resultado = $movimiento->registrarMovimiento($datos);

    if (isset($resultado['success'])) {
        $mensaje = "<div class='alert alert-success text-center mt-3'>✅ Salida registrado correctamente.</div>";
    } else {
        $error = htmlspecialchars($resultado['error'] ?? 'Error desconocido.');
        $mensaje = "<div class='alert alert-danger text-center mt-3'>❌ $error</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Salida</title>
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
        max-width: 600px;
        margin: 50px auto;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        padding: 2rem;
        border-top: 5px solid #8B0000; /* Toque institucional */
    }

    h2 {
        font-weight: bold;
        color: #8B0000;
        text-align: center;
    }

    p {
        color: #2B2B2B;
    }

    .btn-verde {
        background-color: #B38C00; /* Dorado/Ocre */
        color: #fff;
        font-weight: 500;
        border: 1px solid #6F4E37;
        transition: 0.2s;
    }

    .btn-verde:hover {
        background-color: #6F4E37; /* Café Suave */
        border-color: #B38C00;
        color: #fff;
    }

    .btn-gris {
        background-color: #6F4E37; /* Café Suave */
        color: #fff;
        border: 1px solid #8B0000;
        transition: 0.2s;
    }

    .btn-gris:hover {
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

    label {
        font-weight: 500;
        color: #2B2B2B;
    }

    .alert {
        font-size: 0.95rem;
        color: #2B2B2B;
        background-color: #F8F5F0;
        border-color: #B38C00;
    }
</style>


</head>

<body>
    <div class="container">
        <div class="card">
            <h2>➕ Registrar Salida</h2>
            <p class="text-center text-muted mb-4">
                Bienvenido <strong><?= $nombre ?></strong> (<?= $nombreUsuario ?>) — Rol: <?= $rol ?>
            </p>

            <?= $mensaje ?>

            <form method="POST" action="">
                <!-- Producto -->
                <div class="mb-3">
                    <label for="id_producto" class="form-label">Producto</label>
                    <select class="form-select" name="id_producto" id="id_producto" required>
                        <option value="">-- Selecciona un producto --</option>
                        <?php while ($p = $productos->fetch_assoc()): ?>
                            <option value="<?= $p['id_producto'] ?>">
                                <?= htmlspecialchars($p['nombre_producto']) ?> (Stock: <?= $p['stock'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Cantidad -->
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad a prestar</label>
                    <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
                </div>

                <!-- Observación -->
                <div class="mb-3">
                    <label for="observacion" class="form-label">Observación (opcional)</label>
                    <textarea name="observacion" id="observacion" class="form-control" rows="3"
                        placeholder="Notas sobre el Salida..."></textarea>
                </div>

                <!-- Botones -->
                <div class="text-center mt-4">
                    <a href="prestatario.php" class="btn btn-gris px-4">⬅ Volver</a>
                    <button type="submit" class="btn btn-verde px-4">💾 Registrar Salida</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
