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
        body {
            background-color: #f4f6f9;
        }

        .card {
            max-width: 550px;
            margin: 80px auto;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h2 {
            color: #007bff;
            font-weight: bold;
            text-align: center;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #545b62;
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