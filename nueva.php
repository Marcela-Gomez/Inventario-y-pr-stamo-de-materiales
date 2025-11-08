<?php
session_start();
require_once "cn.php";
$cn = new cn();

if (!isset($_SESSION['validado'])) {
    die("No estás autorizado.");
}

$id = $_SESSION['recuperarID'];

$mensaje = "";

if (isset($_POST['cambiar'])) {
    $p1 = trim($_POST['pass1']);
    $p2 = trim($_POST['pass2']);

    if ($p1 === $p2) {

        if(strlen($p1) < 6){
            $mensaje = "⚠️ La contraseña debe tener al menos 6 caracteres.";
        } else {
            $hash = password_hash($p1, PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET contraseña='$hash' WHERE id_usuario=$id";
            $cn->consulta($sql);

            session_destroy();
            header("Location: index.php?ok=1");
            exit;
        }

    } else {
        $mensaje = "❌ Las contraseñas no coinciden.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nueva contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4 mx-auto" style="max-width:450px;">
        <h3 class="text-center mb-3">🔑 Nueva contraseña</h3>

        <?php if($mensaje): ?>
            <div class="alert alert-warning text-center"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST">
            <label class="form-label">Nueva contraseña:</label>
            <input type="password" name="pass1" class="form-control" required>

            <label class="form-label mt-2">Confirmar contraseña:</label>
            <input type="password" name="pass2" class="form-control" required>

            <button name="cambiar" class="btn btn-primary w-100 mt-3">Cambiar 🔁</button>
        </form>
    </div>
</div>

</body>
</html>
