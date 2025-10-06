<?php
// === BACKEND ===
require_once('cn.php');

class addCategoria extends cn {
    public function getCategoria($id_categoria) {
        $sql = "SELECT id_categoria, nombre_categoria FROM categorias WHERE id_categoria = '$id_categoria'";
        return $this->consulta($sql);
    }

    public function getCategorias() {
        $sql = "SELECT * FROM categorias";
        return $this->consulta($sql);
    }

    public function createCategoria($nombre_categoria) {
        $sql = "INSERT INTO categorias (nombre_categoria) VALUES ('$nombre_categoria')";
        return $this->consulta($sql);
    }

    public function updateCategoria($datos) {
        $id_categoria = $datos[0];
        $nombre_categoria = $datos[1];
        $sql = "UPDATE categorias SET nombre_categoria = '$nombre_categoria' WHERE id_categoria = '$id_categoria'";
        return $this->consulta($sql);
    }

    public function deleteCategoria($id_categoria) {
        $sql = "DELETE FROM categorias WHERE id_categoria = '$id_categoria'";
        return $this->consulta($sql);
    }
}

// === FRONTEND ===
$categoria = new addCategoria();

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear'])) {
        $nombre = $_POST['nombre_categoria'];
        $categoria->createCategoria($nombre);
        echo "<script>alert('✅ Categoría creada correctamente');</script>";
    }
    
    if (isset($_POST['actualizar'])) {
        $id_categoria = $_POST['id_categoria'];
        $nombre_categoria = $_POST['nombre_categoria'];
        $datos = [$id_categoria, $nombre_categoria];
        $categoria->updateCategoria($datos);
        echo "<script>alert('✅ Categoría actualizada correctamente');</script>";
    }
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $categoria->deleteCategoria($id);
    echo "<script>alert('✅ Categoría eliminada correctamente'); window.location='addCategoria.php';</script>";
}

// Obtener todas las categorías
$categorias = $categoria->getCategorias();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD de Categorías</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow p-4 mb-4">
                <h4 class="text-center mb-3">
                    <?= isset($_GET['editar']) ? 'Editar Categoría' : 'Agregar Categoría' ?>
                </h4>
                
                <?php if (isset($_GET['editar'])): 
                    $cat_editar = $categoria->getCategoria($_GET['editar']);
                    $cat_data = mysqli_fetch_assoc($cat_editar);
                ?>
                <form method="POST">
                    <input type="hidden" name="id_categoria" value="<?= $cat_data['id_categoria'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Nombre de Categoría</label>
                        <input type="text" name="nombre_categoria" class="form-control" 
                               value="<?= $cat_data['nombre_categoria'] ?>" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="actualizar" class="btn btn-warning">Actualizar</button>
                        <a href="addCategoria.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
                <?php else: ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre de Categoría</label>
                        <input type="text" name="nombre_categoria" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="crear" class="btn btn-success">Crear Categoría</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow p-4">
                <h4 class="text-center mb-4">Lista de Categorías</h4>
                
                <?php if (mysqli_num_rows($categorias) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($categorias)): ?>
                            <tr>
                                <td><?= $row['id_categoria'] ?></td>
                                <td><?= $row['nombre_categoria'] ?></td>
                                <td>
                                    <a href="addCategoria.php?editar=<?= $row['id_categoria'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <a href="addCategoria.php?eliminar=<?= $row['id_categoria'] ?>" class="btn btn-sm btn-danger"
                                       onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info text-center">No hay categorías registradas.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>