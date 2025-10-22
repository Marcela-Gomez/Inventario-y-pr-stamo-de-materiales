<?php
session_start();
require_once('modelo/usuarios.php');

$usuario = new addUsuario();
$error = "";

// Solo procesar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Validar que los campos no estén vacíos
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $contraseña = trim($_POST['contraseña'] ?? '');

    if ($nombre_usuario === '' || $contraseña === '') {
        $error = "⚠️ Por favor complete todos los campos.";
    } else {
        // ✅ Escapar de forma segura SIN tocar cn.php
        $temp_con = new mysqli('localhost', 'root', '', 'inventario1');
        if ($temp_con->connect_error) {
            $error = "❌ Error de conexión con la base de datos.";
        } else {
            $nombre_usuario_seguro = $temp_con->real_escape_string($nombre_usuario);

            // Buscar usuario en la base de datos
            $query = "
                SELECT u.id_usuario, u.nombre, u.nombre_usuario, u.contraseña, r.nombre_rol 
                FROM usuarios u 
                JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.nombre_usuario = '$nombre_usuario_seguro'
                LIMIT 1
            ";

            $result = $usuario->consulta($query);

            if ($result && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);

                // ✅ Comparar contraseña (texto plano)
                if ($contraseña === $user['contraseña']) {
                    // Guardar datos en sesión
                    $_SESSION['usuario'] = [
                        'id' => $user['id_usuario'],
                        'nombre' => $user['nombre'],
                        'usuario' => $user['nombre_usuario'],
                        'rol' => $user['nombre_rol']
                    ];

                    // Redirigir al inicio
                    header("Location: Inicio.php");
                    exit;
                } else {
                    $error = "❌ Contraseña incorrecta.";
                }
            } else {
                $error = "⚠️ Usuario no encontrado.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - Inventario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f2f2f2;
        }

        .login-card {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h3 class="text-center mb-4">Iniciar Sesión</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="nombre_usuario" class="form-control" required minlength="3" maxlength="50"
                    pattern="[A-Za-z0-9_]+" title="Solo letras, números y guiones bajos">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="contraseña" class="form-control" required minlength="3" maxlength="50">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Entrar</button>
            </div>
        </form>
    </div>
</body>

</html>