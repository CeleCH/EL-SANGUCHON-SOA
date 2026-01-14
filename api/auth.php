<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

require_once __DIR__ . '/../servicios/AuthServicio.php';

$auth = new AuthServicio();
$accion = $_GET['accion'] ?? '';

$data = json_decode(file_get_contents("php://input"), true);

if (!$accion) {
    echo json_encode(["error" => "No se envió la acción"]);
    exit;
}

if (!$data) {
    echo json_encode(["error" => "No se recibió JSON"]);
    exit;
}

if ($accion === 'registro') {
    $resultado = $auth->registrar(
        $data['nombre'],
        $data['correo'],
        $data['password']
    );
    echo json_encode(["exito" => $resultado]);
    exit;
}

if ($accion === 'login') {
    $usuario = $auth->login(
        $data['correo'],
        $data['password']
    );

    if ($usuario) {
        echo json_encode([
            "exito" => true,
            "usuario" => [
                "id" => $usuario['id'],
                "nombre" => $usuario['nombre'],
                "rol" => $usuario['rol']
            ]
        ]);
    } else {
        echo json_encode(["exito" => false]);
    }
    exit;
}

echo json_encode(["error" => "Acción no válida"]);

