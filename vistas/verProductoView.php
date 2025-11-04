<?php
require_once('../modelo/addProducto.php');
require_once('../modelo/addCategoria.php');

$productoModel = new addProducto();
$categoriaModel = new addCategoria();

// Eliminar producto si se pasa por GET
if (isset($_GET['eliminar'])) {
    $productoModel->deleteProducto($_GET['eliminar']);
    header("Location: verProductoView.php");
    exit;
}

// Obtener productos y categorías
$productos = $productoModel->getProductos();
$tiene_productos = ($productos && mysqli_num_rows($productos) > 0);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border: none;
            border-radius: 20px;
            padding: 2rem;
        }

        h2 {
            font-weight: 600;
            color: #343a40;
        }

        table th,
        table td {
            vertical-align: middle !important;
        }

        .badge {
            font-size: 0.9rem;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #198754;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .btn-add:hover {
            background-color: #157347;
            color: #fff;
            transform: translateY(-1px);
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .btn-editar {
            background-color: #0d6efd;
            color: white;
            border-radius: 8px;
            padding: 6px 12px;
        }

        .btn-editar:hover {
            background-color: #0b5ed7;
            color: white;
        }

        .btn-eliminar {
            background-color: #dc3545;
            color: white;
            border-radius: 8px;
            padding: 6px 12px;
        }

        .btn-eliminar:hover {
            background-color: #bb2d3b;
            color: white;
        }

        /* Estilo del botón volver */
        .btn-volver {
            background-color: #6c757d;
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .btn-volver:hover {
            background-color: #5c636a;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="header-bar">
                <h2>🛒 Lista de Productos</h2>
                <a href="agregarProducto.php" class="btn btn-add">
                    <i class="bi bi-plus-circle"></i> Agregar Producto
                </a>
            </div>

            <!-- Buscador -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <input type="text" id="buscar" class="form-control" placeholder="Buscar por nombre o categoría...">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100" id="btnBuscar">🔍 Buscar</button>
                </div>
            </div>

            <?php if ($tiene_productos): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center" id="tablaProductos">
                        <thead class="table-success">
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
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($productos)) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_producto']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
                                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_categoria'] ?? 'Sin categoría') ?></td>
                                    <td><?= htmlspecialchars($row['tipo_producto']) ?></td>
                                    <td><?= $row['puede_devolverse'] ? 'Sí' : 'No' ?></td>
                                    <td><?= htmlspecialchars($row['stock']) ?></td>
                                    <td><?= $row['fecha_vencimiento'] !== '0000-00-00' ? htmlspecialchars($row['fecha_vencimiento']) : '-' ?>
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
                                    <td>
                                        <a href="editarProducto.php?id=<?= $row['id_producto'] ?>" class="btn btn-editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="verProductoView.php?eliminar=<?= $row['id_producto'] ?>"
                                            class="btn btn-eliminar"
                                            onclick="return confirm('¿Seguro que deseas eliminar este producto?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    ⚠️ No hay productos registrados todavía.
                </div>
            <?php endif; ?>

            <!-- ✅ Botón Volver al Inicio -->
            <div class="text-center mt-4">
                <a href="../inicio.php" class="btn btn-volver">⬅️ Volver al Inicio</a>
            </div>
        </div>
    </div>

    <!-- Script búsqueda -->
    <script>
        document.getElementById('btnBuscar').addEventListener('click', function () {
            const filtro = document.getElementById('buscar').value.toLowerCase().trim();
            const filas = document.querySelectorAll('#tablaProductos tbody tr');
            let visibles = 0;

            filas.forEach(fila => {
                const texto = fila.textContent.toLowerCase();
                if (texto.includes(filtro)) {
                    fila.style.display = '';
                    visibles++;
                } else {
                    fila.style.display = 'none';
                }
            });

            if (visibles === 0) {
                alert('⚠️ No se encontraron productos con ese criterio de búsqueda.');
            }
        });

        document.getElementById('buscar').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('btnBuscar').click();
            }
        });
    </script>
</body>

</html>