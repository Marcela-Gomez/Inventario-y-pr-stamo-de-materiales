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
$rolModel = new Roles(); // ✅ Corregido

// ✅ Obtener roles desde la base de datos
$roles = $rolModel->getRoles();

// ✅ Manejo de envío del formulario
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $contraseña = trim($_POST['contraseña'] ?? '');
    $id_rol = intval($_POST['id_rol'] ?? 0);

    // 🔍 Validaciones básicas backend
    if ($nombre === '' || $usuario === '' || $contraseña === '' || $id_rol <= 0) {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    } else {
        $resultado = $usuarioModel->createUsuario([$nombre, $usuario, $contraseña, $id_rol]);
        if ($resultado) {
            header("Location: usuariosView.php?exito=1");
            exit;
        } else {
            $mensaje = "❌ Error al agregar el usuario. Intenta nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>
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

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: none;
            color: #dc3545;
        }

        input.is-invalid+.invalid-feedback,
        select.is-invalid+.invalid-feedback {
            display: block;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2 class="text-center mb-4">➕ Agregar Nuevo Usuario</h2>

        <?php if ($mensaje): ?>
            <div class="alert alert-warning text-center"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST" id="formUsuario" novalidate>
            <div class="mb-3">
                <label class="form-label">Nombre completo:</label>
                <input type="text" name="nombre" class="form-control" required placeholder="Ingrese el nombre">
                <div class="invalid-feedback">⚠️ Este campo es obligatorio.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nombre de usuario:</label>
                <input type="text" name="usuario" class="form-control" required placeholder="Ingrese el usuario">
                <div class="invalid-feedback">⚠️ Este campo es obligatorio.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña:</label>
                <input type="password" name="contraseña" class="form-control" required
                    placeholder="Ingrese la contraseña">
                <div class="invalid-feedback">⚠️ Este campo es obligatorio.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Rol:</label>
                <select name="id_rol" class="form-select" required>
                    <option value="">Seleccione un rol</option>
                    <?php if ($roles && $roles->num_rows > 0): ?>
                        <?php while ($rol = $roles->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($rol['id_rol']) ?>">
                                <?= htmlspecialchars($rol['nombre_rol']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option disabled>No hay roles disponibles</option>
                    <?php endif; ?>
                </select>
                <div class="invalid-feedback">⚠️ Debe seleccionar un rol.</div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">💾 Guardar Usuario</button>
                <a href="usuariosView.php" class="btn btn-secondary btn-lg">⬅️ Volver</a>
            </div>
        </form>
    </div>

    <script>
        // 🔹 Validaciones frontend
        document.getElementById('formUsuario').addEventListener('submit', function (e) {
            let valido = true;
            document.querySelectorAll('#formUsuario input, #formUsuario select').forEach(el => {
                if (el.hasAttribute('required') && el.value.trim() === '') {
                    el.classList.add('is-invalid');
                    valido = false;
                } else {
                    el.classList.remove('is-invalid');
                }
            });

            if (!valido) {
                e.preventDefault();
                alert("⚠️ Debe completar todos los campos antes de guardar.");
            }
        });
    </script>
</body>

</html>