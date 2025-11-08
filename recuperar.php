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

        .card {
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 2rem;
        }

        h3 {
            font-weight: bold;
            color: #8B0000; /* Vino Principal */
        }

        .form-label {
            font-weight: 500;
            color: #2B2B2B;
        }

        .alert-warning {
            background-color: #FFF4E5;
            border: 1px solid #B38C00; /* Dorado/Ocre */
            color: #2B2B2B;
            font-size: 0.95rem;
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

        .btn-secondary {
            background-color: #6F4E37; /* Café Suave */
            color: #fff;
            border: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .btn-secondary:hover {
            background-color: #5a3d2d;
            color: #fff;
        }
    </style>
</head>
<body>

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
            <a href="index.php" class="btn btn-secondary w-100 mt-2">⬅ Volver atrás</a>
        </form>
    </div>
</div>

</body>
</html>
