
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../configuracion/base_datos.php';

$conexion = (new BaseDatos())->conectar();

$accion = $_GET['accion'] ?? 'listar';

/* =========================
   📌 LISTAR PRODUCTOS
========================= */
if ($accion === 'listar') {
    $sql = "SELECT * FROM productos";
    $stmt = $conexion->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

/* =========================
   ➕ AGREGAR PRODUCTO (CON IMAGEN)
========================= */
if ($accion === 'agregar') {

    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    // 📸 IMAGEN
    $imagen = $_FILES['imagen'];
    $nombreImagen = time() . "_" . $imagen['name'];
    $rutaDestino = __DIR__ . "/../public/img/" . $nombreImagen;

    move_uploaded_file($imagen['tmp_name'], $rutaDestino);

    $sql = "INSERT INTO productos (nombre, precio, imagen) 
            VALUES (:nombre, :precio, :imagen)";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':precio' => $precio,
        ':imagen' => $nombreImagen
    ]);

    echo json_encode(["exito" => true]);
    exit;
}

/* =========================
   ❌ ELIMINAR PRODUCTO
========================= */
if ($accion === 'eliminar') {

    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    // obtener imagen para borrarla
    $stmt = $conexion->prepare("SELECT imagen FROM productos WHERE id=?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();

    if ($img) {
        @unlink(__DIR__ . "/../public/img/" . $img);
    }

    $stmt = $conexion->prepare("DELETE FROM productos WHERE id=?");
    $stmt->execute([$id]);

    echo json_encode(["exito" => true]);
    exit;
}

echo json_encode(["error" => "Acción no válida"]);
