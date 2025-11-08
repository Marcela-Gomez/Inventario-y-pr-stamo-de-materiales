<?php
session_start();
require_once "cn.php";
$cn = new cn();

$mensaje = "";

if (isset($_POST['buscar'])) {
    $user = $cn->secureSQL(trim($_POST['usuario']));

    $sql = "SELECT id_usuario FROM usuarios WHERE nombre_usuario='$user'";
    $res = $cn->consulta($sql);

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $idUsuario = $row['id_usuario'];

        $sqlP = "SELECT * FROM preguntas_seguridad WHERE id_usuario=$idUsuario";
        $resP = $cn->consulta($sqlP);

        if ($resP->num_rows > 0) {
            $_SESSION['recuperarID'] = $idUsuario;
            header("Location: responder.php");
            exit;
        } else {
            $mensaje = "⚠️ Este usuario no tiene preguntas registradas.";
        }

    } else {
        $mensaje = "❌ El usuario no existe.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recuperar contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4 mx-auto" style="max-width:450px;">
        <h3 class="text-center mb-3">🔐 Recuperar contraseña</h3>

        <?php if($mensaje): ?>
            <div class="alert alert-warning text-center"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST">
            <label class="form-label">Nombre de usuario:</label>
            <input type="text" name="usuario" class="form-control" required>

            <button name="buscar" class="btn btn-primary w-100 mt-3">Buscar preguntas 🔍</button>
            <a href="index.php" class="btn btn-secondary w-100 mt-2">Volver atrás</a>
        </form>
    </div>
</div>

</body>
</html>
