<?php
session_start();

// ✅ Verificar si hay sesión activa
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

// ✅ Cargar el modelo
require_once("../modelo/usuarios.php");

$usuarioModel = new addUsuario();

// ✅ Eliminar usuario si se envía solicitud por GET
if (isset($_GET['eliminar'])) {
    $idEliminar = (int) $_GET['eliminar'];
    if ($usuarioModel->deleteUsuario($idEliminar)) {
        echo "<script>alert('✅ Usuario eliminado correctamente'); window.location='usuariosView.php';</script>";
        exit;
    } else {
        echo "<script>alert('❌ Error al eliminar el usuario'); window.location='usuariosView.php';</script>";
        exit;
    }
}

// ✅ Obtener usuarios con manejo de errores
$result = $usuarioModel->getUsuarios();
$usuarios = [];

if ($result && $result->num_rows > 0) {
    while ($fila = $result->fetch_assoc()) {
        $usuarios[] = $fila;
    }
} else {
    $mensaje = "⚠️ No hay usuarios registrados o ocurrió un error al obtener los datos.";
}

// ✅ Datos de la sesión actual
$usuarioSesion = $_SESSION['usuario'];
$nombre = htmlspecialchars($usuarioSesion['nombre']);
$rol = htmlspecialchars($usuarioSesion['rol']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .card {
            border-radius: 12px;
            position: relative;
        }

        table {
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .btn-editar {
            background-color: #ffc107;
            color: #000;
            border: none;
        }

        .btn-editar:hover {
            background-color: #e0a800;
            color: white;
        }

        .btn-eliminar {
            background-color: #dc3545;
            color: #fff;
            border: none;
        }

        .btn-eliminar:hover {
            background-color: #bb2d3b;
        }

        /* 🔹 Botón flotante en esquina superior derecha */
        .btn-agregar {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow p-4">

            <!-- 🔹 Botón para agregar nuevo usuario -->
            <a href="agregarUsuarios.php" class="btn btn-primary btn-agregar">➕ Agregar Usuario</a>

            <h2 class="text-center mb-4">👥 Gestión de Usuarios</h2>
            <p class="text-center text-muted">
                Sesión activa: <strong><?= $nombre ?></strong> (Rol: <?= $rol ?>)
            </p>
            <hr>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-warning text-center"><?= htmlspecialchars($mensaje) ?></div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars($u['id_usuario']) ?></td>
                                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                                    <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
                                    <td><?= htmlspecialchars($u['nombre_rol']) ?></td>
                                    <td>
                                        <a href="editarUsuario.php?id=<?= $u['id_usuario'] ?>" class="btn btn-editar btn-sm">
                                            ✏️ Editar
                                        </a>
                                        <a href="?eliminar=<?= $u['id_usuario'] ?>" class="btn btn-eliminar btn-sm"
                                            onclick="return confirm('⚠️ ¿Estás seguro de eliminar este usuario?');">
                                            🗑 Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="../Inicio.php" class="btn btn-secondary">⬅️ Volver al Inicio</a>
            </div>
        </div>
    </div>
</body>

</html>