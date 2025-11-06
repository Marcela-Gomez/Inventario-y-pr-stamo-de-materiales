<?php
session_start();

// ✅ Verificar sesión activa
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

// ✅ Cargar modelos
require_once("../modelo/usuarios.php");
require_once("../modelo/roles.php");

$usuarioModel = new addUsuario();
$rolModel = new Roles();

$id_usuario = $_GET['id'] ?? null;
if (!$id_usuario || !is_numeric($id_usuario)) {
    header("Location: usuariosView.php?error=ID inválido");
    exit;
}

// ✅ Obtener datos del usuario
$usuario = $usuarioModel->getUsuarioById($id_usuario);
if (!$usuario) {
    header("Location: usuariosView.php?error=Usuario no encontrado");
    exit;
}

// ✅ Obtener roles
$roles = $rolModel->getRoles();

$mensaje = "";

// ✅ Si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $nombre_usuario = trim($_POST['usuario'] ?? '');
    $id_rol = intval($_POST['id_rol'] ?? 0);

    if ($nombre === '' || $nombre_usuario === '' || $id_rol <= 0) {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    } else {
        $resultado = $usuarioModel->updateUsuarioSinPassword($id_usuario, $nombre, $nombre_usuario, $id_rol);
        if ($resultado) {
            header("Location: usuariosView.php?actualizado=1");
            exit;
        } else {
            $mensaje = "❌ Error al actualizar el usuario.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .card {
            border-radius: 15px;
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #007bff;
            font-weight: bold;
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
        <h2 class="text-center mb-4">✏️ Editar Usuario</h2>

        <?php if ($mensaje): ?>
            <div class="alert alert-warning text-center"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label">Nombre completo:</label>
                <input type="text" name="nombre" class="form-control"
                    value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nombre de usuario:</label>
                <input type="text" name="usuario" class="form-control"
                    value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Rol:</label>
                <select name="id_rol" class="form-select" required>
                    <option value="">Seleccione un rol</option>
                    <?php if ($roles && $roles->num_rows > 0): ?>
                        <?php while ($rol = $roles->fetch_assoc()): ?>
                            <option value="<?= $rol['id_rol'] ?>" <?= ($usuario['id_rol'] == $rol['id_rol']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rol['nombre_rol']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option disabled>No hay roles disponibles</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">💾 Guardar Cambios</button>
                <a href="usuariosView.php" class="btn btn-secondary btn-lg">⬅️ Volver</a>
            </div>
        </form>
    </div>
</body>

</html>