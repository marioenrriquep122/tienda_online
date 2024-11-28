<?php
require '../config/config.php';
require '../config/database.php';

$db = new Database();
$con = $db->conectar();

// Depuración: guarda datos enviados en un log para revisar errores (puedes eliminar esto después).
file_put_contents('log.txt', print_r($_POST, true), FILE_APPEND);

$response = ['ok' => false];

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

    session_start(); // Asegúrate de que la sesión esté activa.

    if ($action === 'agregar' && $id > 0 && $cantidad > 0) {
        $_SESSION['carrito']['productos'][$id] = $cantidad;

        // Busca el precio del producto.
        $stmt = $con->prepare("SELECT precio, descuento FROM productos WHERE id = ? AND activo = 1 LIMIT 1");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            $precio = $producto['precio'];
            $descuento = $producto['descuento'];
            $precio_descuento = $precio - ($precio * $descuento / 100);
            $subtotal = $cantidad * $precio_descuento;

            $response['ok'] = true;
            $response['sub'] = number_format($subtotal, 2, '.', ',');
        }
    } elseif ($action === 'eliminar' && $id > 0) {
        if (isset($_SESSION['carrito']['productos'][$id])) {
            unset($_SESSION['carrito']['productos'][$id]);
            $response['ok'] = true;
        } else {
            $response['error'] = 'Producto no encontrado.';
        }
    } else {
        $response['error'] = 'Acción no válida o datos incompletos.';
    }
} else {
    $response['error'] = 'No se recibió ninguna acción.';
}

// Siempre devuelve JSON para que el frontend pueda procesar.
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
