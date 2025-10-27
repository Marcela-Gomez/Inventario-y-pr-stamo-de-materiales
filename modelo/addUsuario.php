<?php
require_once('cn.php');

class addUsuario extends cn {

    public function getUsuario($id_usuario) {
        $sql = "SELECT u.id_usuario, u.nombre, u.nombre_usuario, r.nombre_rol 
                FROM usuarios u 
                JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.id_usuario = '$id_usuario'";
        return $this->consulta($sql);
    }

    public function getUsuarios() {
        $sql = "SELECT u.id_usuario, u.nombre, u.nombre_usuario, r.nombre_rol 
                FROM usuarios u 
                JOIN roles r ON u.id_rol = r.id_rol";
        return $this->consulta($sql);
    }

    public function createUsuario($datos) {
        $nombre = $datos[0];
        $nombre_usuario = $datos[1];
        $contraseña = $datos[2];
        $id_rol = $datos[3];

        $sql = "INSERT INTO usuarios (nombre, nombre_usuario, contraseña, id_rol)
                VALUES ('$nombre', '$nombre_usuario', '$contraseña', '$id_rol')";
        $this->consulta($sql);
    }

    public function updateUsuario($datos) {
        $id_usuario = $datos[0];
        $nombre = $datos[1];
        $nombre_usuario = $datos[2];
        $contraseña = $datos[3];
        $id_rol = $datos[4];

        $sql = "UPDATE usuarios 
                SET nombre = '$nombre',
                    nombre_usuario = '$nombre_usuario',
                    contraseña = '$contraseña',
                    id_rol = '$id_rol'
                WHERE id_usuario = '$id_usuario'";
        $this->consulta($sql);
    }

    public function deleteUsuario($id_usuario) {
        $sql = "DELETE FROM usuarios WHERE id_usuario = '$id_usuario'";
        $this->consulta($sql);
    }
}
?>
