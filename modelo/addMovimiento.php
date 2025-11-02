<?php
require_once(__DIR__ . '/../cn.php');

class addMovimiento extends cn
{
    public function __construct()
    {
        parent::__construct();
    }

    public function registrarMovimiento($datos)
    {
        $id_producto = (int) $datos['id_producto'];
        $cantidad = (int) $datos['cantidad'];
        $tipo_movimiento = trim($this->con->real_escape_string($datos['tipo_movimiento'] ?? ''));
        $observacion = trim($this->con->real_escape_string($datos['observacion'] ?? ''));
        $id_usuario = 0;
        if(isset($datos['id_comprador'])) {
            $id_usuario = (int) $datos['id_comprador'];
        } elseif (isset($datos['id_prestatario'])) {
            $id_usuario = (int) $datos['id_prestatario'];
        } elseif (isset($datos['id_prestamista'])) {
            $id_usuario = (int) $datos['id_prestamista'];
        } else {
            return ['error' => '❌ No se proporcionó un ID de usuario válido.'];
        }

        // ✅ Verificar tipo_movimiento no vacío
        if (empty($tipo_movimiento)) {
            error_log("❌ ERROR: tipo_movimiento vacío en registrarMovimiento()\n", 3, "debug_movimientos.log");
            return ['error' => '❌ No se recibió el tipo de movimiento.'];
        }

        // ✅ Verificar que el usuario exista
        $checkUser = $this->consulta("SELECT id_usuario FROM usuarios WHERE id_usuario = '$id_usuario' LIMIT 1");
        if (!$checkUser || $checkUser->num_rows === 0) {
            return ['error' => "⚠️ El usuario con ID $id_usuario no existe en la base de datos."];
        }

        // ✅ Verificar producto
        $res = $this->consulta("SELECT stock, puede_devolverse FROM productos WHERE id_producto = '$id_producto'");
        $producto = $res ? $res->fetch_assoc() : null;

        if (!$producto) {
            return ['error' => '❌ El producto no existe.'];
        }

        // ⚠️ Verificar stock si es salida o préstamo
        if (in_array($tipo_movimiento, ['Salida', 'Prestamo']) && $cantidad > (int) $producto['stock']) {
            return ['error' => '⚠️ No hay suficiente stock disponible.'];
        }

        // 🔄 Actualizar stock
        switch ($tipo_movimiento) {
            case 'Compra':
            case 'Entrada':
                $this->consulta("UPDATE productos SET stock = stock + $cantidad WHERE id_producto = '$id_producto'");
                break;
            case 'Salida':
            case 'Prestamo':
                $this->consulta("UPDATE productos SET stock = stock - $cantidad WHERE id_producto = '$id_producto'");
                break;
            case 'Devolucion':
                $this->consulta("UPDATE productos SET stock = stock + $cantidad WHERE id_producto = '$id_producto'");
                break;
            default:
                return ['error' => '❌ Tipo de movimiento no reconocido.'];
        }

        // 🧾 Preparar INSERT según tipo
        switch ($tipo_movimiento) {
            case 'Compra':
            case 'Entrada':
                $sql = "INSERT INTO movimientos (id_producto, tipo_movimiento, cantidad, observacion, id_comprador)
                        VALUES ('$id_producto', '$tipo_movimiento', '$cantidad', '$observacion', '$id_usuario')";
                break;

            case 'Salida':
            case 'Prestamo':
                $sql = "INSERT INTO movimientos (id_producto, tipo_movimiento, cantidad, observacion, id_prestatario)
                        VALUES ('$id_producto', '$tipo_movimiento', '$cantidad', '$observacion', '$id_usuario')";
                break;

            case 'Devolucion':
                $sql = "INSERT INTO movimientos (id_producto, tipo_movimiento, cantidad, observacion, id_prestamista)
                        VALUES ('$id_producto', '$tipo_movimiento', '$cantidad', '$observacion', '$id_usuario')";
                break;
        }

        // 💾 Insertar movimiento
        $resultado = $this->consulta($sql);
        if ($resultado) {
            return ['success' => true, 'puede_devolverse' => (bool) $producto['puede_devolverse']];
        } else {
            error_log("❌ ERROR SQL: $sql\n", 3, "debug_movimientos.log");
            return ['error' => '❌ No se pudo registrar el movimiento. Revisa debug_movimientos.log'];
        }
    }

    public function getPrestamosActivos($id_prestatario)
    {
        $id_prestatario = (int) $id_prestatario;
        $sql = "SELECT m.id_movimiento, p.nombre_producto, m.cantidad, p.puede_devolverse, 
                       m.tipo_movimiento, m.fecha_movimiento
                FROM movimientos m
                INNER JOIN productos p ON m.id_producto = p.id_producto
                WHERE m.id_prestatario = '$id_prestatario'
                  AND m.tipo_movimiento = 'Prestamo'";
        return $this->consulta($sql);
    }
}
?>