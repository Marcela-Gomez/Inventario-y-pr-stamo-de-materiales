<?php
session_start();
require_once "cn.php";
$cn = new cn();

$id = $_SESSION['recuperarID'];

$sql = "SELECT id, pregunta, respuesta FROM preguntas_seguridad WHERE id_usuario=$id";
$res = $cn->consulta($sql);

$preguntas = [];
while ($row = $res->fetch_assoc()) {
    $preguntas[] = $row;
}

if (count($preguntas) <= 0) {
    die("Este usuario no tiene preguntas registradas.");
}

$mensaje = "";

// --- VALIDACIÓN ---
if (isset($_POST['validar'])) {

    $correctas = 0; 
    $total = count($preguntas);

    foreach ($preguntas as $row) {
        $idPregunta = $row['id'];
        $respuestaGuardada = strtolower(trim($row['respuesta']));
        $respuestaUsuario = strtolower(trim($_POST['resp_'.$idPregunta] ?? ''));

        if ($respuestaUsuario === $respuestaGuardada) {
            $correctas++;
        }
    }

    if ($correctas === $total) {
        $_SESSION['validado'] = true;
        header("Location: nueva.php");
        exit;
    } else {
        $mensaje = "❌ Alguna respuesta es incorrecta.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Responder preguntas</title>
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

        .alert-danger {
            background-color: #FDECEA;
            border: 1px solid #9B001F; /* Rojo Ladrillo */
            color: #2B2B2B;
            font-size: 0.95rem;
        }

        .btn-success {
            background-color: #198754; /* Verde institucional */
            border: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .btn-success:hover {
            background-color: #157347;
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
    <div class="card shadow p-4 mx-auto" style="max-width:600px;">
        <h3 class="text-center mb-3">🧠 Verificación de Seguridad</h3>

        <?php if($mensaje): ?>
            <div class="alert alert-danger text-center"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST">
            <?php foreach ($preguntas as $row) { ?>
                <label class="form-label"><?= $row['pregunta']; ?></label>
                <input type="text" name="resp_<?= $row['id']; ?>" class="form-control mb-3" required>
            <?php } ?>

            <button name="validar" class="btn btn-success w-100">Validar ✅</button>
            <a href="buscar.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
        </form>
    </div>
</div>

</body>
</html>
