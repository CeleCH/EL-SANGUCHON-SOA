
<?php
require_once __DIR__ . '/../configuracion/base_datos.php';

class PedidoServicio {

    private $conexion;

    public function __construct() {
        $db = new BaseDatos();
        $this->conexion = $db->conectar();
    }

    public function crearPedido($usuario_id, $productos) {
        $this->conexion->beginTransaction();

        $sqlPedido = "INSERT INTO pedidos (usuario_id, total) VALUES (:usuario, 0)";
        $stmtPedido = $this->conexion->prepare($sqlPedido);
        $stmtPedido->execute([':usuario' => $usuario_id]);
        $pedido_id = $this->conexion->lastInsertId();

        $total = 0;

        foreach ($productos as $p) {
            $subtotal = $p['precio'] * $p['cantidad'];
            $total += $subtotal;

            $sqlDetalle = "INSERT INTO pedido_detalle 
                (pedido_id, producto_id, cantidad, precio)
                VALUES (:pedido, :producto, :cantidad, :precio)";
            $stmt = $this->conexion->prepare($sqlDetalle);
            $stmt->execute([
                ':pedido' => $pedido_id,
                ':producto' => $p['id'],
                ':cantidad' => $p['cantidad'],
                ':precio' => $p['precio']
            ]);
        }

        $sqlTotal = "UPDATE pedidos SET total = :total WHERE id = :id";
        $stmtTotal = $this->conexion->prepare($sqlTotal);
        $stmtTotal->execute([
            ':total' => $total,
            ':id' => $pedido_id
        ]);

        $this->conexion->commit();
        return $pedido_id;
    }
   public function listarPedidos() {
    $sql = "SELECT 
                p.id,
                IFNULL(u.nombre, 'Sin usuario') AS cliente,
                p.total,
                IFNULL(p.estado, 'pendiente') AS estado,
                p.creado_en
            FROM pedidos p
            LEFT JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY p.id DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function actualizarEstado($pedido_id, $estado) {
    $sql = "UPDATE pedidos SET estado = :estado WHERE id = :id";
    $stmt = $this->conexion->prepare($sql);
    return $stmt->execute([
        ':estado' => $estado,
        ':id' => $pedido_id
    ]);
}

}
