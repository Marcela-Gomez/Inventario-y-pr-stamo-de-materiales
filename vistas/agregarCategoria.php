<?php
session_start();
require_once("../modelo/addCategoria.php");

// 🔒 Verificar sesión activa
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

$categoriaModel = new addCategoria();
$mensaje = "";
$categoriaEditar = null;

// 🔹 Si viene con ?editar=ID, obtener datos de esa categoría
if (isset($_GET['editar'])) {
    $cat = $categoriaModel->getCategoria($_GET['editar']);
    $categoriaEditar = mysqli_fetch_assoc($cat);
}

// 🔹 Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_categoria']);

    if ($nombre === "") {
        $mensaje = "⚠️ El nombre no puede estar vacío.";
    } else {
        if (isset($_POST['actualizar'])) {
            $id = $_POST['id_categoria'];
            $categoriaModel->updateCategoria([$id, $nombre]);
            echo "<script>alert('✅ Categoría actualizada correctamente'); window.location='verCategoria.php';</script>";
            exit;
        } else {
            $categoriaModel->createCategoria($nombre);
            echo "<script>alert('✅ Categoría creada correctamente'); window.location='verCategoria.php';</script>";
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= $categoriaEditar ? 'Editar Categoría' : 'Agregar Categoría' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

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
            font-family: 'Segoe UI', sans-serif;
            color: #2B2B2B;
        }

        .card {
            max-width: 550px;
            margin: 80px auto;
            border-radius: 18px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
            padding: 35px;
            background-color: #fff;
            border-left: 8px solid #8B0000;
            border-top: 3px solid #B38C00;
        }

        h2 {
            color: #8B0000;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        hr {
            border: 0;
            height: 2px;
            background: linear-gradient(to right, #8B0000, #B38C00);
            margin-bottom: 25px;
        }

        label {
            font-weight: 600;
            color: #6F4E37;
        }

        input.form-control {
            border: 2px solid #E0CDA9;
            border-radius: 10px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input.form-control:focus {
            border-color: #B38C00;
            box-shadow: 0 0 5px rgba(179, 140, 0, 0.4);
        }

        /* -------------------------
           BOTONES PERSONALIZADOS
           ------------------------- */
        .btn-primary {
            background-color: #8B0000;
            border: none;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #660000;
            transform: translateY(-1px);
        }

        .btn-success {
            background-color: #B38C00;
            border: none;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            color: #fff;
        }

        .btn-success:hover {
            background-color: #8C6F00;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #6F4E37;
            border: none;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #523828;
            transform: translateY(-1px);
        }

        /* -------------------------
           ALERTAS Y MISC
           ------------------------- */
        .alert-warning {
            color: #8B0000;
            background-color: #FFF4E0;
            border: 1px solid #FFD580;
            border-radius: 10px;
        }

        .d-grid button,
        .d-grid a {
            font-size: 1rem;
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2><?= $categoriaEditar ? '✏️ Editar Categoría' : '➕ Agregar Nueva Categoría' ?></h2>
        <hr>

        <?php if ($mensaje): ?>
            <div class="alert alert-warning text-center"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST">
            <?php if ($categoriaEditar): ?>
                <input type="hidden" name="id_categoria" value="<?= $categoriaEditar['id_categoria'] ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Nombre de Categoría:</label>
                <input type="text" name="nombre_categoria" class="form-control"
                    value="<?= $categoriaEditar['nombre_categoria'] ?? '' ?>" required>
            </div>

            <div class="d-grid gap-2">
                <?php if ($categoriaEditar): ?>
                    <button type="submit" name="actualizar" class="btn btn-primary">💾 Actualizar</button>
                <?php else: ?>
                    <button type="submit" name="crear" class="btn btn-success">💾 Guardar</button>
                <?php endif; ?>
                <a href="verCategoria.php" class="btn btn-secondary">⬅️ Volver</a>
            </div>
        </form>
    </div>
</body>

</html>