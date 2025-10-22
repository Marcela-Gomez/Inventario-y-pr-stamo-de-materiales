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
        }

        table {
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h2 class="text-center mb-4">👥 Gestión de Usuarios</h2>
            <p class="text-center text-muted">
                Sesión activa: <strong><?= $nombre ?></strong> (Rol: <?= $rol ?>)
            </p>
            <hr>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-warning text-center"><?= htmlspecialchars($mensaje) ?></div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars($u['id_usuario']) ?></td>
                                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                                    <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
                                    <td><?= htmlspecialchars($u['nombre_rol']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="../Inicio.php" class="btn btn-secondary">⬅️ Volver al Inicio</a>
                <a href="../logout.php" class="btn btn-danger">🚪 Cerrar Sesión</a>
            </div>
        </div>
    </div>
</body>

</html>