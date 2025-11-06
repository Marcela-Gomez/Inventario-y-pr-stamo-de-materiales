<?php
session_start();
require_once("../modelo/addCategoria.php");

// 🔒 Verificar sesión activa
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

$categoriaModel = new addCategoria();

// 🚫 Eliminar categoría si viene por GET
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $categoriaModel->deleteCategoria($id);
    echo "<script>alert('✅ Categoría eliminada correctamente'); window.location='verCategoria.php';</script>";
    exit;
}

// 🔹 Obtener todas las categorías
$categorias = $categoriaModel->getCategorias();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>📦 Lista de Categorías</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .btn-agregar {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-agregar:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">📦 Categorías Registradas</h2>
                <a href="agregarCategoria.php" class="btn btn-agregar">➕ Nueva Categoría</a>
            </div>
            <hr>

            <?php if ($categorias && mysqli_num_rows($categorias) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre de Categoría</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($categorias)): ?>
                                <tr>
                                    <td><?= $row['id_categoria'] ?></td>
                                    <td><?= htmlspecialchars($row['nombre_categoria']) ?></td>
                                    <td>
                                        <a href="agregarCategoria.php?editar=<?= $row['id_categoria'] ?>"
                                            class="btn btn-sm btn-warning">✏️ Editar</a>
                                        <a href="verCategoria.php?eliminar=<?= $row['id_categoria'] ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Seguro que deseas eliminar esta categoría?');">
                                            🗑️ Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">No hay categorías registradas.</div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="../inicio.php" class="btn btn-secondary">⬅️ Volver al Inicio</a>
            </div>
        </div>
    </div>
</body>

</html>