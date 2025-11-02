<?php
require_once(__DIR__ . '/../cn.php');

class addUsuario extends cn
{
    public function __construct()
    {
        parent::__construct(); // ✅ Inicializa la conexión desde cn.php
    }

    /**
     * ✅ Obtiene un usuario por su ID
     */
    public function getUsuarioById($id_usuario)
    {
        if (empty($id_usuario) || !is_numeric($id_usuario)) {
            return false;
        }

        $id_usuario = (int) $id_usuario;
        $sql = "SELECT u.id_usuario, u.nombre, u.nombre_usuario, u.id_rol, r.nombre_rol
                FROM usuarios u
                JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.id_usuario = '$id_usuario'";

        $result = $this->consulta($sql);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : false;
    }

    /**
     * ✅ Obtiene todos los usuarios
     */
    public function getUsuarios()
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.nombre_usuario, r.nombre_rol 
                FROM usuarios u 
                JOIN roles r ON u.id_rol = r.id_rol";

        return $this->consulta($sql);
    }

    /**
     * ✅ Crea un nuevo usuario
     */
    public function createUsuario($datos)
    {
        if (count($datos) < 4) {
            return false;
        }

        $nombre = trim($this->con->real_escape_string($datos[0]));
        $nombre_usuario = trim($this->con->real_escape_string($datos[1]));
        $contraseña = trim($this->con->real_escape_string($datos[2]));
        $id_rol = (int) $datos[3];

        if ($nombre === '' || $nombre_usuario === '' || $contraseña === '' || $id_rol <= 0) {
            return false;
        }

        $sql = "INSERT INTO usuarios (nombre, nombre_usuario, contraseña, id_rol)
                VALUES ('$nombre', '$nombre_usuario', '$contraseña', '$id_rol')";

        return $this->consulta($sql);
    }

    /**
     * ✅ Actualiza los datos de un usuario (sin cambiar contraseña)
     */
    public function updateUsuarioSinPassword($id_usuario, $nombre, $nombre_usuario, $id_rol)
    {
        if (empty($id_usuario) || !is_numeric($id_usuario)) {
            return false;
        }

        $id_usuario = (int) $id_usuario;
        $nombre = trim($this->con->real_escape_string($nombre));
        $nombre_usuario = trim($this->con->real_escape_string($nombre_usuario));
        $id_rol = (int) $id_rol;

        if ($nombre === '' || $nombre_usuario === '' || $id_rol <= 0) {
            return false;
        }

        $sql = "UPDATE usuarios 
                SET nombre = '$nombre',
                    nombre_usuario = '$nombre_usuario',
                    id_rol = '$id_rol'
                WHERE id_usuario = '$id_usuario'";

        return $this->consulta($sql);
    }

    /**
     * ✅ Elimina un usuario
     */
    public function deleteUsuario($id_usuario)
    {
        if (empty($id_usuario) || !is_numeric($id_usuario)) {
            return false;
        }

        $id_usuario = (int) $id_usuario;
        $sql = "DELETE FROM usuarios WHERE id_usuario = '$id_usuario'";
        return $this->consulta($sql);
    }
}
?>