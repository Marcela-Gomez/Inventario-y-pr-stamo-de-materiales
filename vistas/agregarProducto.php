<?php
require_once('../modelo/addProducto.php');
require_once('../modelo/addCategoria.php');

$producto = new addProducto();
$categoria = new addCategoria();

// Obtener las categorías para llenar el <select>
$categorias = $categoria->getCategorias();

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Validaciones Backend ---
    $nombre_producto = trim($_POST['nombre_producto']);
    $descripcion = trim($_POST['descripcion']);
    $id_categoria = $_POST['id_categoria'];
    $tipo_producto = $_POST['tipo_producto'];
    $puede_devolverse = isset($_POST['puede_devolverse']) ? 1 : 0;
    $stock = $_POST['stock'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $estado = $_POST['estado'];

    if (empty($nombre_producto))
        $errores[] = "El nombre del producto es obligatorio.";
    if (empty($id_categoria))
        $errores[] = "Debe seleccionar una categoría.";
    if ($stock < 0)
        $errores[] = "El stock no puede ser negativo.";
    if (!empty($fecha_vencimiento) && strtotime($fecha_vencimiento) < strtotime(date('Y-m-d')))
        $errores[] = "La fecha de vencimiento no puede ser anterior a hoy.";

    if($tipo == 'Perecedero' && empty($fecha_vencimiento)) {
        $errores[] = "La fecha de vencimiento es obligatoria para productos perecederos.";
    }
    if($tipo == 'No Perecedero' && !empty($fecha_vencimiento)) {
        $errores[] = "La fecha de vencimiento debe estar vacía para productos no perecederos.";
    }
    // Si no hay errores, guardar producto
    if (empty($errores)) {
        $datos = [
            $nombre_producto,
            $descripcion,
            $id_categoria,
            $tipo_producto,
            $puede_devolverse,
            $stock,
            !empty($fecha_vencimiento) ? $fecha_vencimiento : 'NULL',
            $estado
        ];

        $producto->createProducto($datos);

        echo "<script>alert('✅ Producto agregado correctamente'); window.location='verProductoView.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        .top-right-btn {
            position: absolute;
            top: 15px;
            right: 15px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5 position-relative">
        <!-- 🔹 Botón arriba a la derecha -->
        <a href="verProductoView.php" class="btn btn-info top-right-btn">👁 Ver Productos</a>

        <div class="card shadow p-4">
            <h2 class="text-center mb-4">Agregar Producto</h2>

            <!-- Mostrar errores del backend -->
            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="formProducto" novalidate>
                <div class="mb-3">
                    <label class="form-label">Nombre del Producto *</label>
                    <input type="text" name="nombre_producto" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categoría *</label>
                    <select name="id_categoria" class="form-select" required>
                        <option value="">Seleccione una categoría</option>
                        <?php while ($row = mysqli_fetch_assoc($categorias)) { ?>
                            <option value="<?= $row['id_categoria'] ?>"><?= $row['nombre_categoria'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de Producto *</label>
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stock *</label>
                        <input type="number" name="stock" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de vencimiento (si aplica)</label>
                        <input type="date" name="fecha_vencimiento" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Estado *</label>
                    <select name="estado" class="form-select" required>
                        <option value="Disponible">Disponible</option>
                        <option value="Agotado">Agotado</option>
                        <option value="Descontinuado">Descontinuado</option>
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">Guardar Producto</button>
                    <a href="verProductoView.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- 🔹 Validaciones Frontend -->
    <script>
        document.getElementById('formProducto').addEventListener('submit', function (e) {
            let nombre = document.querySelector('[name="nombre_producto"]').value.trim();
            let categoria = document.querySelector('[name="id_categoria"]').value;
            let stock = parseFloat(document.querySelector('[name="stock"]').value);
            let fecha = document.querySelector('[name="fecha_vencimiento"]').value;
            let errores = [];

            if (nombre === "") errores.push("El nombre del producto es obligatorio.");
            if (categoria === "") errores.push("Debe seleccionar una categoría.");
            if (isNaN(stock) || stock < 0) errores.push("El stock debe ser un número mayor o igual a 0.");
            if (fecha !== "" && new Date(fecha) < new Date()) errores.push("La fecha de vencimiento no puede ser anterior a hoy.");

            if (errores.length > 0) {
                e.preventDefault();
                alert("⚠️ Corrige los siguientes errores:\n\n" + errores.join("\n"));
            }
        });
    </script>
</body>

</html>