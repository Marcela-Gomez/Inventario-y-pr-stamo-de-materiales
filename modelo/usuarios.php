<?php
require_once(__DIR__ . '/../cn.php');

class addUsuario extends cn
{
    public function __construct()
    {
        parent::__construct(); // ✅ Inicializa la conexión desde cn.php
    }

    /**
     * Obtiene un usuario por su ID
     */
    public function getUsuario($id_usuario)
    {
        if (empty($id_usuario) || !is_numeric($id_usuario)) {
            return false; // 🚫 ID inválido
        }

        $id_usuario = (int) $id_usuario;
        $sql = "SELECT u.id_usuario, u.nombre, u.nombre_usuario, r.nombre_rol 
                FROM usuarios u 
                JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.id_usuario = '$id_usuario'";

        return $this->consulta($sql);
    }

    /**
     * Obtiene todos los usuarios
     */
    public function getUsuarios()
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.nombre_usuario, r.nombre_rol 
                FROM usuarios u 
                JOIN roles r ON u.id_rol = r.id_rol";

        return $this->consulta($sql);
    }

    /**
     * Crea un nuevo usuario
     */
    public function createUsuario($datos)
    {
        if (count($datos) < 4) {
            return false; // 🚫 Faltan datos
        }

        // Sanitizar entrada
        $nombre = trim($this->con->real_escape_string($datos[0]));
        $nombre_usuario = trim($this->con->real_escape_string($datos[1]));
        $contraseña = trim($this->con->real_escape_string($datos[2]));
        $id_rol = (int) $datos[3];

        if ($nombre === '' || $nombre_usuario === '' || $contraseña === '' || $id_rol <= 0) {
            return false; // 🚫 Campos vacíos o inválidos
        }

        $sql = "INSERT INTO usuarios (nombre, nombre_usuario, contraseña, id_rol)
                VALUES ('$nombre', '$nombre_usuario', '$contraseña', '$id_rol')";

        return $this->consulta($sql);
    }

    /**
     * Actualiza los datos de un usuario
     */
    public function updateUsuario($datos)
    {
        if (count($datos) < 5) {
            return false; // 🚫 Faltan datos
        }

        $id_usuario = (int) $datos[0];
        $nombre = trim($this->con->real_escape_string($datos[1]));
        $nombre_usuario = trim($this->con->real_escape_string($datos[2]));
        $contraseña = trim($this->con->real_escape_string($datos[3]));
        $id_rol = (int) $datos[4];

        if ($id_usuario <= 0 || $nombre === '' || $nombre_usuario === '' || $contraseña === '' || $id_rol <= 0) {
            return false; // 🚫 Datos inválidos
        }

        $sql = "UPDATE usuarios 
                SET nombre = '$nombre',
                    nombre_usuario = '$nombre_usuario',
                    contraseña = '$contraseña',
                    id_rol = '$id_rol'
                WHERE id_usuario = '$id_usuario'";

        return $this->consulta($sql);
    }

    /**
     * Elimina un usuario por ID
     */
    public function deleteUsuario($id_usuario)
    {
        if (empty($id_usuario) || !is_numeric($id_usuario)) {
            return false; // 🚫 ID inválido
        }

        $id_usuario = (int) $id_usuario;
        $sql = "DELETE FROM usuarios WHERE id_usuario = '$id_usuario'";

        return $this->consulta($sql);
    }
}
?>