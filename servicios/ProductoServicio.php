
<?php
require_once __DIR__ . '/../configuracion/base_datos.php';

class ProductoServicio {

    private $conexion;

    public function __construct() {
        $db = new BaseDatos();
        $this->conexion = $db->conectar();
    }

    public function listarProductos() {
        $sql = "SELECT * FROM productos WHERE activo = 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
}
