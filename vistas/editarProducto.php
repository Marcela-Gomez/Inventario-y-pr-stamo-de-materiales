<?php
require_once('../modelo/addProducto.php');
require_once('../modelo/addCategoria.php');

$productoModel = new addProducto();
$categoriaModel = new addCategoria();

if (!isset($_GET['id'])) {
    header("Location: verProductoView.php");
    exit;
}

$id_producto = $_GET['id'];
$producto = $productoModel->getProducto($id_producto);
$prod_data = mysqli_fetch_assoc($producto);

if (!$prod_data) {
    echo "<script>alert('❌ Producto no encontrado'); window.location='verProductoView.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $id_producto = $_POST['id_producto'];
    $nombre_producto = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $id_categoria = $_POST['id_categoria'];
    $tipo_producto = $_POST['tipo_producto'];
    $puede_devolverse = isset($_POST['puede_devolverse']) ? 1 : 0;
    $stock = $_POST['stock'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $estado = $_POST['estado'];

    // ✅ Conservar precio actual

    $datos = [
        $id_producto,
        $nombre_producto,
        $descripcion,
        $id_categoria,
        $tipo_producto,
        $puede_devolverse,
        $stock,
        $fecha_vencimiento,
        $estado
    ];

    $productoModel->updateProducto($datos);

    echo "<script>alert('✅ Producto actualizado correctamente'); window.location='verProductoView.php';</script>";
    exit;
}

$categorias = $categoriaModel->getCategorias();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
    body {
        background-color: #F8F5F0;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        color: #2B2B2B;
    }

    .card {
        border: none;
        border-radius: 20px;
        padding: 2rem;
        max-width: 800px;
        margin: 3rem auto;
        background-color: white;
        box-shadow: 0 4px 15px rgba(139, 0, 0, 0.2);
    }

    h2 {
        font-weight: 600;
        color: #8B0000;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .btn-guardar {
        background-color: #8B0000;
        color: white;
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 8px;
        transition: 0.2s;
    }

    .btn-guardar:hover {
        background-color: #9B001F;
    }

    .btn-cancelar {
        background-color: #6F4E37;
        color: white;
        font-weight: 500;
        border-radius: 8px;
        padding: 10px 20px;
        transition: 0.2s;
    }

    .btn-cancelar:hover {
        background-color: #5C3B2E;
    }

    input, textarea, select {
        border: 1px solid #B38C00;
    }

    input:focus, textarea:focus, select:focus {
        border-color: #8B0000;
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
        outline: none;
    }
</style>

</head>

<body>
    <div class="container">
        <div class="card shadow">
            <h2>✏️ Editar Producto</h2>
            <form method="POST">
                <input type="hidden" name="id_producto" value="<?= htmlspecialchars($prod_data['id_producto']) ?>">

                <div class="mb-3">
                    <label class="form-label">Nombre del Producto</label>
                    <input type="text" name="nombre_producto" class="form-control"
                        value="<?= htmlspecialchars($prod_data['nombre_producto']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"
                        required><?= htmlspecialchars($prod_data['descripcion']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <select name="id_categoria" class="form-select" required>
                        <option value="">Seleccione una categoría</option>
                        <?php while ($cat = mysqli_fetch_assoc($categorias)) { ?>
                            <option value="<?= $cat['id_categoria'] ?>"
                                <?= ($cat['id_categoria'] == $prod_data['id_categoria']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre_categoria']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Producto</label>
                        <input type="text" name="tipo_producto" class="form-control"
                            value="<?= htmlspecialchars($prod_data['tipo_producto']) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">¿Devolvible?</label><br>
                        <input type="checkbox" name="puede_devolverse" <?= $prod_data['puede_devolverse'] ? 'checked' : '' ?>>
                        <span class="ms-2">Sí</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control"
                            value="<?= htmlspecialchars($prod_data['stock']) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control"
                            value="<?= ($prod_data['fecha_vencimiento'] !== '0000-00-00') ? htmlspecialchars($prod_data['fecha_vencimiento']) : '' ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" required>
                        <option value="Disponible" <?= ($prod_data['estado'] === 'Disponible') ? 'selected' : '' ?>>
                            Disponible</option>
                        <option value="Agotado" <?= ($prod_data['estado'] === 'Agotado') ? 'selected' : '' ?>>Agotado
                        </option>
                        <option value="Descontinuado" <?= ($prod_data['estado'] === 'Descontinuado') ? 'selected' : '' ?>>
                            Descontinuado</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="verProductoView.php" class="btn btn-cancelar">
                        <i class="bi bi-arrow-left-circle"></i> Cancelar
                    </a>
                    <button type="submit" name="actualizar" class="btn btn-guardar">
                        💾 Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>