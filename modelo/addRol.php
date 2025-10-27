<?php
require_once('cn.php');

class addRol extends cn {

    public function getRol($id_rol) {
        $sql = "SELECT id_rol, nombre_rol FROM roles WHERE id_rol = '$id_rol'";
        return $this->consulta($sql);
    }

    public function getRoles() {
        $sql = "SELECT * FROM roles";
        return $this->consulta($sql);
    }

    public function createRol($nombre_rol) {
        $sql = "INSERT INTO roles (nombre_rol) VALUES ('$nombre_rol')";
        $this->consulta($sql);
    }

    public function updateRol($datos) {
        $id_rol = $datos[0];
        $nombre_rol = $datos[1];

        $sql = "UPDATE roles SET nombre_rol = '$nombre_rol' WHERE id_rol = '$id_rol'";
        $this->consulta($sql);
    }

    public function deleteRol($id_rol) {
        $sql = "DELETE FROM roles WHERE id_rol = '$id_rol'";
        $this->consulta($sql);
    }
}
?>
