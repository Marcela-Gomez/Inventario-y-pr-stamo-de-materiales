<?php
session_start();
require_once('modelo/usuarios.php');

$usuario = new addUsuario();
$error = "";

// Solo procesar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $contraseña = trim($_POST['contraseña'] ?? '');

    if ($nombre_usuario === '' || $contraseña === '') {
        $error = "⚠️ Por favor complete todos los campos.";
    } else {
        $temp_con = new mysqli('localhost', 'root', '', 'inventario1');
        if ($temp_con->connect_error) {
            $error = "❌ Error de conexión con la base de datos.";
        } else {
            $nombre_usuario_seguro = $temp_con->real_escape_string($nombre_usuario);

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

if (password_verify($contraseña, $user['contraseña'])) {
                    // ✅ Guardar sesión
                    $_SESSION['usuario'] = [
                        'id' => $user['id_usuario'],
                        'nombre' => $user['nombre'],
                        'usuario' => $user['nombre_usuario'],
                        'rol' => $user['nombre_rol']
                    ];

                    // ✅ Redirigir según el rol
                    switch (strtolower($user['nombre_rol'])) {
                        case 'administrador':
                            header("Location: Inicio.php");
                            break;
                        case 'prestamista':
                            header("Location: vistas/prestamista.php");
                            break;
                        case 'prestatario':
                            header("Location: vistas/prestatario.php");
                            break;
                        case 'comprador':
                            header("Location: vistas/comprador.php");
                            break;
                        default:
                            header("Location: index.php");
                            break;
                    }
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
    /* ============================================================
       🎨 PALETA ITCA-FEPADE
       ------------------------------------------------------------
       - Vino Principal:  #8B0000
       - Rojo Ladrillo:   #9B001F
       - Dorado/Ocre:     #B38C00
       - Café Suave:      #6F4E37
       - Fondo Claro:     #F8F5F0
       - Texto Oscuro:    #2B2B2B
    ============================================================ */

    body {
        background-color: #F8F5F0;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-card {
        max-width: 400px;
        margin: 100px auto;
        padding: 30px;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        border-top: 5px solid #8B0000; /* Vino Principal */
    }

    h3 {
        text-align: center;
        font-weight: 600;
        color: #8B0000; /* Vino Principal */
    }

    .form-control {
        border: 1px solid #6F4E37; /* Café Suave */
    }

    .form-control:focus {
        border-color: #8B0000;
        box-shadow: 0 0 5px rgba(139, 0, 0, 0.5);
    }

    .btn-primary {
        background-color: #8B0000; /* Vino Principal */
        border: none;
        font-weight: 500;
        transition: 0.2s;
    }

    .btn-primary:hover {
        background-color: #9B001F; /* Rojo Ladrillo */
    }

    .alert-danger {
        background-color: #F8F5F0;
        border: 1px solid #9B001F;
        color: #2B2B2B;
        font-size: 0.95rem;
    }

    a {
        color: #8B0000;
        text-decoration: none;
    }

    a:hover {
        color: #9B001F;
        text-decoration: underline;
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
        <a href="recuperar.php" class="d-block text-center mt-3">¿Olvidaste tu contraseña?</a>
    </div>
</body>

</html>