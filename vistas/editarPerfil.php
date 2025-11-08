<?php
session_start();
require_once '../cn.php';
$cn = new cn();
$conn = $cn->getCon();

if(!isset($_SESSION['usuario'])){
    header("Location: index.php");
    exit();
}

$id_usuario = $_SESSION['usuario']['id'];

// Obtener datos del usuario
$query = $conn->prepare("SELECT nombre, nombre_usuario, contraseña, id_rol FROM usuarios WHERE id_usuario = ?");
$query->bind_param("i", $id_usuario);
$query->execute();
$result = $query->get_result();
$usuario = $result->fetch_assoc();
$query->close();

// Actualizar datos
if(isset($_POST['actualizar_perfil'])){
    $nombre = $_POST['nombre'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $password = trim($_POST['contraseña']);

    // Si el campo viene vacío -> mantener contraseña actual
    if(empty($password)){
        $password = $usuario['contraseña'];
    } else {
        // Verificar si ya está hasheada
        if(!password_verify($password, $usuario['contraseña'])){
            $password = password_hash($password, PASSWORD_BCRYPT);
        }
    }

    $update = $conn->prepare("UPDATE usuarios SET nombre = ?, nombre_usuario = ?, contraseña = ? WHERE id_usuario = ?");
    $update->bind_param("sssi", $nombre, $nombre_usuario, $password, $id_usuario);
    $update->execute();
    $update->close();
    header("Location: editarPerfil.php?msg=Perfil actualizado");
}

// Registrar preguntas:
if(isset($_POST['guardar_preguntas'])){
    $pregunta1 = $_POST['pregunta1'];
    $respuesta1 = password_hash($_POST['respuesta1'], PASSWORD_BCRYPT);

    $insert = $conn->prepare("INSERT INTO preguntas_seguridad (id_usuario, pregunta, respuesta) VALUES (?, ?, ?)");
    $insert->bind_param("iss", $id_usuario, $pregunta1, $respuesta1);
    $insert->execute();
    $insert->close();
}

// Obtener preguntas
$qPreg = $conn->prepare("SELECT id, pregunta, respuesta FROM preguntas_seguridad WHERE id_usuario = ?");
$qPreg->bind_param("i", $id_usuario);
$qPreg->execute();
$preguntas = $qPreg->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Editar Perfil</title>
<style>
    /* ============================================================
       🎨 PALETA ITCA-FEPADE
       ------------------------------------------------------------
       - Vino Principal:        #8B0000
       - Rojo Ladrillo:         #9B001F
       - Dorado/Ocre:           #B38C00
       - Café Suave:            #6F4E37
       - Fondo Claro:           #F8F5F0
       - Texto Oscuro:          #2B2B2B
       ============================================================ */
    body{
        font-family: Arial, sans-serif;
        background: #F8F5F0;
        padding: 20px;
        color: #2B2B2B;
    }
    .card{
        background:white;
        padding:20px;
        width:400px;
        margin:auto 0 auto 0;
        border-radius:10px;
        box-shadow:0 0 10px rgba(0,0,0,0.2);
        margin-bottom: 30px;
    }
    input{
        width:100%;
        padding:8px;
        margin:5px 0;
        border-radius:5px;
        border:1px solid #B38C00;
    }
    input:focus {
        border-color: #8B0000;
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
        outline: none;
    }
    button{
        background:#8B0000;
        color:white;
        padding:10px;
        border:none;
        border-radius:5px;
        cursor:pointer;
        width:100%;
        font-weight: 500;
        transition: 0.2s;
    }
    button:hover{
        background:#9B001F;
    }
    h2,h3{
        text-align:center;
        color: #8B0000;
    }
    .text-success {
        color: #B38C00 !important;
    }
    label {
        font-weight: 500;
    }
</style>
</head>
<body>

<div class="card">
<h2>Editar Perfil</h2>

<?php if(isset($_GET['msg'])): ?>
<p class="text-success"><?php echo $_GET['msg']; ?></p>
<?php endif; ?>

<form method="POST">
    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>"> 

    <label>Usuario:</label>
    <input type="text" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario['nombre_usuario']); ?>">

    <label>Nueva Contraseña (opcional):</label>
    <input type="password" name="password" placeholder="Dejar vacío para no cambiar">

    <br><br>
    <button type="submit" name="actualizar_perfil">Actualizar</button>
</form>
</div>

<div class="card">
<h3>Preguntas de Seguridad</h3>

<?php if($preguntas->num_rows == 0): ?>
<form method="POST">
    <label>Pregunta:</label>
    <input type="text" name="pregunta1">

    <label>Respuesta:</label>
    <input type="text" name="respuesta1">

    <button type="submit" name="guardar_preguntas">Guardar</button>
</form>
<?php endif; ?>

<?php while($p = $preguntas->fetch_assoc()): ?>
<form method="POST">
    <input type="hidden" name="id_pregunta" value="<?php echo $p['id']; ?>">
    
    <label>Pregunta:</label>
    <input type="text" name="pregunta1" value="<?php echo htmlspecialchars($p['pregunta']); ?>">

    <label>Nueva Respuesta:</label>
    <input type="text" name="respuesta1">

    <button type="submit" name="editar_pregunta">Actualizar</button>
</form>
<br>
<?php endwhile; ?>
</div>

</body>

<?php
// Editar pregunta existente (hash incluido)
if(isset($_POST['editar_pregunta'])){
    $pregunta = $_POST['pregunta1'];
    $respuesta = password_hash($_POST['respuesta1'], PASSWORD_BCRYPT);
    $idpreg = $_POST['id_pregunta'];

    $updatePreg = $conn->prepare("UPDATE preguntas_seguridad SET pregunta=?, respuesta=? WHERE id=? AND id_usuario=?");
    $updatePreg->bind_param("ssii", $pregunta, $respuesta, $idpreg, $id_usuario);
    $updatePreg->execute();
    $updatePreg->close();
    header("Location: editarPerfil.php?msg=Pregunta actualizada");
}
?>
</html>
