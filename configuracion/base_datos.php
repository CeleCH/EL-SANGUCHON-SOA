
<?php
class BaseDatos {
    private $host = "localhost";
    private $db = "el_sanguchon_db";
    private $user = "root";
    private $pass = "";
    private $conexion;

    public function conectar() {
        try {
            $this->conexion = new PDO(
                "mysql:host={$this->host};dbname={$this->db};charset=utf8",
                $this->user,
                $this->pass
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conexion;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
