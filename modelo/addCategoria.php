<?php
// === BACKEND ===
require_once(__DIR__ . '/../cn.php');

class addCategoria extends cn {
    public function getCategoria($id_categoria) {
        $sql = "SELECT id_categoria, nombre_categoria FROM categorias WHERE id_categoria = '$id_categoria'";
        return $this->consulta($sql);
    }

    public function getCategorias() {
        $sql = "SELECT * FROM categorias";
        return $this->consulta($sql);
    }

    public function createCategoria($nombre_categoria) {
        $sql = "INSERT INTO categorias (nombre_categoria) VALUES ('$nombre_categoria')";
        return $this->consulta($sql);
    }

    public function updateCategoria($datos) {
        $id_categoria = $datos[0];
        $nombre_categoria = $datos[1];
        $sql = "UPDATE categorias SET nombre_categoria = '$nombre_categoria' WHERE id_categoria = '$id_categoria'";
        return $this->consulta($sql);
    }

    public function deleteCategoria($id_categoria) {
        $sql = "DELETE FROM categorias WHERE id_categoria = '$id_categoria'";
        return $this->consulta($sql);
    }
}
?>