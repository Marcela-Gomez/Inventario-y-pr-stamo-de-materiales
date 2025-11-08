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
</head>
<body class="bg-light">

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
