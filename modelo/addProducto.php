<?php
require_once(__DIR__ . '/../cn.php');

class addProducto extends cn
{

    public function getProducto($id_producto)
    {
        $sql = "SELECT p.*, c.nombre_categoria 
                FROM productos p
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.id_producto = '$id_producto'";
        return $this->consulta($sql);
    }

    public function getProductos()
    {
        $sql = "SELECT p.*, c.nombre_categoria 
                FROM productos p
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria";
        return $this->consulta($sql);
    }

    public function createProducto($datos)
    {
        $nombre_producto = $datos[0];
        $descripcion = $datos[1];
        $id_categoria = $datos[2];
        $tipo_producto = $datos[3];
        $puede_devolverse = $datos[4];
        $stock = $datos[5];
        $fecha_vencimiento = $datos[6];
        $estado = $datos[7];

        $sql = "INSERT INTO productos (nombre_producto, descripcion, id_categoria, tipo_producto, puede_devolverse, stock, fecha_vencimiento, estado)
                VALUES ('$nombre_producto', '$descripcion', '$id_categoria', '$tipo_producto', '$puede_devolverse', '$stock', '$fecha_vencimiento', '$estado')";
        $this->consulta($sql);
    }

    public function updateProducto($datos)
    {
        $id_producto = $datos[0];
        $nombre_producto = $datos[1];
        $descripcion = $datos[2];
        $id_categoria = $datos[3];
        $tipo_producto = $datos[4];
        $puede_devolverse = $datos[5];
        $stock = $datos[6];
        $fecha_vencimiento = $datos[7];
        $estado = $datos[8];

        $sql = "UPDATE productos SET 
                    nombre_producto = '$nombre_producto',
                    descripcion = '$descripcion',
                    id_categoria = '$id_categoria',
                    tipo_producto = '$tipo_producto',
                    puede_devolverse = '$puede_devolverse',
                    stock = '$stock',
                    fecha_vencimiento = '$fecha_vencimiento',
                    estado = '$estado'
                WHERE id_producto = '$id_producto'";
        $this->consulta($sql);
    }

    public function deleteProducto($id_producto)
    {
        $sql = "DELETE FROM productos WHERE id_producto = '$id_producto'";
        $this->consulta($sql);
    }

    // 🔍 Obtener producto por ID (para registrar compra)
    public function obtenerProductoPorId($id_producto)
    {
        $sql = "SELECT * FROM productos WHERE id_producto = '$id_producto' LIMIT 1";
        $resultado = $this->consulta($sql);

        if ($resultado && $resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        } else {
            return null;
        }
    }

    // 🔄 Actualizar solo el stock de un producto
    public function actualizarStock($id_producto, $nuevoStock)
    {
        $sql = "UPDATE productos SET stock = '$nuevoStock' WHERE id_producto = '$id_producto'";
        $this->consulta($sql);
    }
}
?>