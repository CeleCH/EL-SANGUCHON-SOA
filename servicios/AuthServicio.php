
<?php
require_once __DIR__ . '/../configuracion/base_datos.php';

class AuthServicio {

    private $conexion;

    public function __construct() {
        $db = new BaseDatos();
        $this->conexion = $db->conectar();
    }

    public function registrar($nombre, $correo, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (nombre, correo, password) 
                VALUES (:nombre, :correo, :password)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':password' => $hash
        ]);
    }

    public function login($correo, $password) {
        $sql = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return false;
    }
}
