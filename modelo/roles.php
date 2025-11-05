<?php
require_once(__DIR__ . '/../cn.php');

class Roles extends cn
{
    public function __construct()
    {
        parent::__construct(); // ✅ Inicializa la conexión con la base de datos
    }

    /**
     * 🔹 Obtener todos los roles existentes
     */
    public function getRoles()
    {
        $sql = "SELECT id_rol, nombre_rol FROM roles ORDER BY id_rol ASC";
        return $this->consulta($sql);
    }

    /**
     * 🔹 Obtener un rol por su ID
     */
    public function getRol($id_rol)
    {
        if (empty($id_rol) || !is_numeric($id_rol)) {
            return false; // 🚫 ID inválido
        }

        $id_rol = (int) $id_rol;
        $sql = "SELECT id_rol, nombre_rol FROM roles WHERE id_rol = '$id_rol'";
        return $this->consulta($sql);
    }

    /**
     * 🔹 Agregar un nuevo rol
     */
    public function addRol($nombre_rol)
    {
        if (empty($nombre_rol)) {
            return false; // 🚫 Nombre vacío
        }

        $nombre_rol = trim($this->con->real_escape_string($nombre_rol));
        $sql = "INSERT INTO roles (nombre_rol) VALUES ('$nombre_rol')";
        return $this->consulta($sql);
    }

    /**
     * 🔹 Actualizar un rol existente
     */
    public function updateRol($id_rol, $nombre_rol)
    {
        if (empty($id_rol) || empty($nombre_rol)) {
            return false; // 🚫 Datos inválidos
        }

        $id_rol = (int) $id_rol;
        $nombre_rol = trim($this->con->real_escape_string($nombre_rol));

        $sql = "UPDATE roles SET nombre_rol = '$nombre_rol' WHERE id_rol = '$id_rol'";
        return $this->consulta($sql);
    }

    /**
     * 🔹 Eliminar un rol por ID
     */
    public function deleteRol($id_rol)
    {
        if (empty($id_rol) || !is_numeric($id_rol)) {
            return false; // 🚫 ID inválido
        }

        $id_rol = (int) $id_rol;
        $sql = "DELETE FROM roles WHERE id_rol = '$id_rol'";
        return $this->consulta($sql);
    }
}
?>