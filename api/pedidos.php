<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

require_once __DIR__ . '/../servicios/PedidoServicio.php';

$pedidoServicio = new PedidoServicio();
$accion = $_GET['accion'] ?? '';

// 👉 LISTAR PEDIDOS (GET)
if ($accion === 'listar') {
    echo json_encode($pedidoServicio->listarPedidos());
    exit;
}

// 👉 ACTUALIZAR ESTADO (POST)
if ($accion === 'estado') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(["error" => "Datos no recibidos"]);
        exit;
    }

    $pedidoServicio->actualizarEstado(
        $data['pedido_id'],
        $data['estado']
    );

    echo json_encode(["exito" => true]);
    exit;
}

// 👉 CREAR PEDIDO (POST)
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Datos no recibidos"]);
    exit;
}

$pedido_id = $pedidoServicio->crearPedido(
    $data['usuario_id'],
    $data['productos']
);

echo json_encode([
    "exito" => true,
    "pedido_id" => $pedido_id
]);
