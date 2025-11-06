<?php
// === BACKEND ===
require_once(__DIR__ . '/../cn.php');

class addCategoria extends cn
{
    public function __construct()
    {
        parent::__construct(); // inicializa conexión
    }

    /**
     * 🔹 Obtener una categoría por su ID
     */
    public function getCategoria($id_categoria)
    {
        if (empty($id_categoria) || !is_numeric($id_categoria)) {
            return false; // 🚫 ID inválido
        }

        $id_categoria = (int) $id_categoria;
        $sql = "SELECT id_categoria, nombre_categoria 
                FROM categorias 
                WHERE id_categoria = '$id_categoria'";

        $resultado = $this->consulta($sql);
        return ($resultado && mysqli_num_rows($resultado) > 0) ? $resultado : false;
    }

    /**
     * 🔹 Obtener todas las categorías
     */
    public function getCategorias()
    {
        $sql = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY id_categoria ASC";
        return $this->consulta($sql);
    }

    /**
     * 🔹 Crear una nueva categoría
     */
    public function createCategoria($nombre_categoria)
    {
        if (empty(trim($nombre_categoria))) {
            return false; // 🚫 No se permite vacío
        }

        $nombre_categoria = trim($this->con->real_escape_string($nombre_categoria));
        $sql = "INSERT INTO categorias (nombre_categoria) VALUES ('$nombre_categoria')";
        return $this->consulta($sql);
    }

    /**
     * 🔹 Actualizar una categoría existente
     */
    public function updateCategoria($datos)
    {
        if (count($datos) < 2) {
            return false; // 🚫 Faltan datos
        }

        $id_categoria = (int) $datos[0];
        $nombre_categoria = trim($this->con->real_escape_string($datos[1]));

        if ($id_categoria <= 0 || $nombre_categoria === '') {
            return false;
        }

        $sql = "UPDATE categorias 
                SET nombre_categoria = '$nombre_categoria' 
                WHERE id_categoria = '$id_categoria'";

        return $this->consulta($sql);
    }

    /**
     * 🔹 Eliminar una categoría por ID
     */
    public function deleteCategoria($id_categoria)
    {
        if (empty($id_categoria) || !is_numeric($id_categoria)) {
            return false;
        }

        $id_categoria = (int) $id_categoria;
        $sql = "DELETE FROM categorias WHERE id_categoria = '$id_categoria'";
        return $this->consulta($sql);
    }
}
?>