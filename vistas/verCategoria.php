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
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        border-top: 5px solid #8B0000; /* Toque institucional */
        background-color: #fff;
        padding: 2rem;
    }

    h2 {
        color: #8B0000;
        font-weight: bold;
    }

    th {
        background-color: #8B0000; /* Vino Principal */
        color: #F8F5F0;
        text-transform: uppercase;
    }

    .btn-agregar {
        background-color: #B38C00; /* Dorado/Ocre */
        color: #fff;
        border: 1px solid #6F4E37;
        transition: 0.3s;
    }

    .btn-agregar:hover {
        background-color: #6F4E37; /* Café Suave */
        border-color: #B38C00;
        color: #fff;
    }

    .btn-warning {
        background-color: #B38C00; /* Dorado/Ocre */
        color: #fff;
        border: 1px solid #6F4E37;
        transition: 0.2s;
    }

    .btn-warning:hover {
        background-color: #6F4E37; /* Café Suave */
        border-color: #B38C00;
        color: #fff;
    }

    .btn-danger {
        background-color: #9B001F; /* Rojo Ladrillo */
        color: #fff;
        border: 1px solid #8B0000;
        transition: 0.2s;
    }

    .btn-danger:hover {
        background-color: #8B0000; /* Vino Principal */
        border-color: #9B001F;
        color: #fff;
    }

    .btn-secondary {
        background-color: #6F4E37; /* Café Suave */
        color: #fff;
        border: 1px solid #8B0000;
    }

    .btn-secondary:hover {
        background-color: #9B001F; /* Rojo Ladrillo */
        border-color: #8B0000;
        color: #fff;
    }

    .alert {
        background-color: #F8F5F0;
        border-color: #B38C00;
        color: #2B2B2B;
        font-size: 0.95rem;
    }

    table {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #6F4E37;
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