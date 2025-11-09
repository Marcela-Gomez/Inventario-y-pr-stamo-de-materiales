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
        border-radius: 12px;
        position: relative;
        border-top: 5px solid #8B0000; /* Toque institucional */
        background-color: #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        padding: 2rem;
    }

    h2 {
        color: #8B0000;
        font-weight: bold;
    }

    p {
        color: #2B2B2B;
    }

    table {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #6F4E37;
    }

    th {
        background-color: #8B0000;
        color: #F8F5F0;
        text-transform: uppercase;
    }

    .btn-agregar {
        position: absolute;
        top: 20px;
        right: 20px;
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

    .btn-editar {
        background-color: #B38C00; /* Dorado/Ocre */
        color: #fff;
        border: 1px solid #6F4E37;
        transition: 0.2s;
    }

    .btn-editar:hover {
        background-color: #6F4E37; /* Café Suave */
        border-color: #B38C00;
        color: #fff;
    }

    .btn-eliminar {
        background-color: #9B001F; /* Rojo Ladrillo */
        color: #fff;
        border: 1px solid #8B0000;
        transition: 0.2s;
    }

    .btn-eliminar:hover {
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
</style>


</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="../inicio.php">
            🛍 Panel Principal
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="btn btn-success me-2 mb-2" href="verProductoView.php">
                        🛒 Ver Productos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-primary me-2 mb-2" href="usuariosView.php">
                        👥 Gestionar Usuarios
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-warning me-2 mb-2" href="verCategoria.php">
                        📦 Ver Categorías
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-info me-2 mb-2" href="graficos.php">
                        📊 Gráficos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-info me-2 mb-2" href="graficosMensuales.php">
                        📈 Gráficos Mensuales
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-danger me-2 mb-2" href="../logout.php">
                        🚪 Cerrar Sesión
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>
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