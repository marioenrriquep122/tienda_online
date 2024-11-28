<?php
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();


$producto = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
$lista_carrito = [];

if ($producto != null) {
    foreach ($producto as $clave => $cantidad) {
        // Prepara la consulta
        $sql = $con->prepare("SELECT id, nombre, precio, descuento, ? AS cantidad 
                              FROM productos 
                              WHERE id = ? AND activo = 1");
        $sql->execute([$cantidad, $clave]);

        // Obtén el resultado
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $lista_carrito[] = $resultado; // Agrega el producto al carrito.
        } else {
            echo "Producto con ID $clave no encontrado o no está activo.<br>";
        }
    }
} else {
    header("Location: index.php");
    exit;
}


if (isset($_GET['logout'])) {
    session_unset(); // Limpia todas las variables de sesión
    session_destroy(); // Destruye la sesión
    header("Location: index.php"); // Redirige a la página principal
    exit;
}









?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tienda Online</title>



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

</head>

<body>

    <header>
        <div class="navbar  navbar-expand-lg navbar-dark bg-dark ">
            <div class="container">
                <a href="login.php" class="navbar-brand">
                    <strong>Tienda Online</strong>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarHeader">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link active">Catalogo</a>
                        </li>
                        <li class="nav-item">
                            <a href="contacto.php" class="nav-link active">Contacto O reporte </a>
                        </li>

                    </ul>



                    <a href="checkout.php" class="btn btn-primary me-3">
                        <i class="fas fa-shopping-cart"></i> Carrito
                        <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart; ?></span>
                    </a>

                    <!-- Mostrar botones según el estado de la sesión -->
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <!-- Si el usuario ha iniciado sesión -->
                        <a href="#" class="btn btn-success me-2"><i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?></a>
                        <a href="?logout=true" class="btn btn-danger">Cerrar Sesión</a>
                    <?php } else { ?>
                        <!-- Si el usuario no ha iniciado sesión -->
                        <a href="login.php" class="btn btn-success"><i class="fas fa-user"></i> Ingresar</a>
                    <?php } ?>

                </div>

            </div>
        </div>
    </header>

    <main>
        <div class="container">

            <div class="row">
                <div class="col-6">
                    <h4>Detalles de pago</h4>
                    <div id="paypal-button-container"></div>

                </div>

                <div class="col-6">



                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Subtotal</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($lista_carrito)) {
                                    echo '<tr><td colspan="5" class="text-center"><b>Lista vacía</b></td></tr>';
                                } else {
                                    $total = 0;

                                    foreach ($lista_carrito as $producto) {
                                        $id = $producto['id'];
                                        $nombre = $producto['nombre'];
                                        $precio = $producto['precio'];
                                        $descuento = $producto['descuento'];
                                        $cantidad = $producto['cantidad'];
                                        $precio_desc = $precio - ($precio * $descuento / 100);
                                        $subtotal = $cantidad * $precio_desc;
                                        $total += $subtotal;
                                ?>
                                        <tr>

                                            <td><?php echo $nombre; ?></td>


                                            <td>
                                                <div id="subtotal_<?php echo $id; ?>" name="subtotal[]">
                                                    <?php echo MONEDA . number_format($subtotal, 2, '.', ','); ?>
                                                </div>
                                            </td>

                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="2">
                                            <p class="h3 text-end" id="total"><?php echo MONEDA . number_format($total, 2, '.', ','); ?></p>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-5 offset-md-7 d-grid gap-2">
                            <button class="btn btn-primary btn-lg">
                                Realizar Pago
                            </button>

                        </div>

                    </div>



                </div>
            </div>
        </div>

    </main>








    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=ATqyo2V83zIKcb8N4msFRPqfX_CT5Qtjf5mMGBR45cRIIF3IWtsYUEfJ5265Xk5lbCPzYXCPOApYimTo&currency=USD"></script>


    <script>
        paypal.Buttons({
            style: {
                color: 'blue',
                shape: 'pill',
                label: 'pay',
            },
            createOrder: function(data, actions) {
                // Obtén el total desde el DOM
                const totalElement = document.getElementById('total');
                if (!totalElement) {
                    console.error('Elemento #total no encontrado.');
                    alert('No se pudo obtener el total para el pago.');
                    return;
                }

                // Limpia el valor del total (elimina comas y cualquier carácter no numérico)
                let total = totalElement.textContent.replace(/[^\d.-]/g, ''); // Solo números, puntos y guiones
                total = parseFloat(total); // Convierte a número

                // Valida el total
                if (isNaN(total) || total <= 0) {
                    console.error('Total no válido:', total);
                    alert('El total calculado no es válido.');
                    return;
                }

                console.log('Total para la orden:', total); // Depuración

                // Crea la orden con el total dinámico
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: total.toFixed(2) // Asegura que tenga dos decimales
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(detalles) {
                    console.log('Pago aprobado:', detalles);

                    // Muestra el alert y espera a que el usuario presione "OK"
                    alert('Se ha hecho efectiva la compra. ¡Muchas gracias y esperamos que compre pronto!');

                    // Espera 5 segundos antes de redirigir
                    setTimeout(function() {
                        window.location.href = "index.php";
                    }, 5000); // 5000 ms = 5 segundos
                }).catch(function(error) {
                    console.error('Error al capturar el pago:', error);
                });
            },
            onCancel: function(data) {
                alert('Pago cancelado');
                console.log(data);
            },
            onError: function(err) {
                console.error('Error en PayPal:', err);
                alert('Hubo un error procesando el pago.');
            }
        }).render('#paypal-button-container');
    </script>











</body>

</html>