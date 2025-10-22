<?php
require_once('../modelo/addProducto.php');
require_once('../modelo/addCategoria.php');

$producto = new addProducto();
$categoria = new addCategoria();

// Obtener las categorías para llenar el <select>
$categorias = $categoria->getCategorias();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_producto = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $id_categoria = $_POST['id_categoria'];
    $tipo_producto = $_POST['tipo_producto'];
    $puede_devolverse = isset($_POST['puede_devolverse']) ? 1 : 0;
    $stock = $_POST['stock'];
    $precio = $_POST['precio'];
    $fecha_vencimiento = !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : 'NULL';
    $estado = $_POST['estado'];

    // Arreglo con los datos que pide tu modelo
    $datos = [
        $nombre_producto,
        $descripcion,
        $id_categoria,
        $tipo_producto,
        $puede_devolverse,
        $stock,
        $precio,
        $fecha_vencimiento,
        $estado
    ];

    // Insertar producto
    $producto->createProducto($datos);

    echo "<script>alert('✅ Producto agregado correctamente'); window.location='listar_productos.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h2 class="text-center mb-4">Agregar Producto</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Nombre del Producto</label>
                    <input type="text" name="nombre_producto" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <select name="id_categoria" class="form-select" required>
                        <option value="">Seleccione una categoría</option>
                        <?php while ($row = mysqli_fetch_assoc($categorias)) { ?>
                            <option value="<?= $row['id_categoria'] ?>"><?= $row['nombre_categoria'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de Producto</label>
                    <select name="tipo_producto" class="form-select" required>
                        <option value="Perecedero">Perecedero</option>
                        <option value="No Perecedero">No Perecedero</option>
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="puede_devolverse" class="form-check-input" id="devolvible">
                    <label for="devolvible" class="form-check-label">Puede devolverse</label>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" name="precio" class="form-control" step="0.01" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha de vencimiento (si aplica)</label>
                        <input type="date" name="fecha_vencimiento" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="Disponible">Disponible</option>
                        <option value="Agotado">Agotado</option>
                        <option value="Descontinuado">Descontinuado</option>
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">Guardar Producto</button>
                    <a href="listar_productos.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>