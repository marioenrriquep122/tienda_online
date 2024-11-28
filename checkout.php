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
}



//session_destroy();


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
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
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
                                    <td><?php echo htmlspecialchars($nombre); ?></td>
                                    <td><?php echo MONEDA . number_format($precio_desc, 2, '.', ','); ?></td>
                                    <td>
                                        <input type="number" min="1" max="10" step="1"
                                            value="<?php echo $cantidad; ?>" size="5"
                                            id="cantidad_<?php echo $id; ?>"
                                            onchange="actualizaCantidad(this.value, <?php echo $id; ?>)">
                                    </td>
                                    <td>
                                        <div id="subtotal_<?php echo $id; ?>" name="subtotal[]">
                                            <?php echo MONEDA . number_format($subtotal, 2, '.', ','); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a id="eliminar_<?php echo $id; ?>" class="btn btn-warning btn-sm"
                                            data-id="<?php echo $id; ?>"
                                            onclick="prepararEliminar(this.dataset.id)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#eliminarModal">Eliminar</a>


                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                            <tr>
                                <td colspan="3"><b>Total</b></td>
                                <td><?php echo MONEDA . number_format($total, 2, '.', ','); ?></td>
                                <td></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>


            <?php if ($lista_carrito != null) { ?>
                <div class="row">
                    <div class="col-md-5 offset-md-7 d-grid gap-2">
                        <a href="pago.php" class="btn btn-primary btn-lg">
                            Realizar Pago
                        </a>

                    </div>

                </div>

            <?php } ?>






        </div>

    </main>


    <!-- Modal -->
    <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarModalLabel">Alerta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Desea eliminar el producto de la lista?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="btn-elimina" type="button" class="btn btn-danger" onclick="eliminar()">Eliminar</button>

                </div>
            </div>
        </div>
    </div>







    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>





    <script>
        function actualizarTotal() {
            let total = 0;
            const subtotales = document.querySelectorAll('div[name="subtotal[]"]');
            subtotales.forEach((sub) => {
                let value = parseFloat(sub.textContent.replace(/[^\d.-]/g, ''));
                total += isNaN(value) ? 0 : value;
            });

            const totalElement = document.querySelector('tr:last-child td:nth-child(2)');
            if (totalElement) {
                totalElement.textContent = `$${total.toFixed(2)}`;
            }
        }




        function prepararEliminar(id) {
            let botonElimina = document.getElementById('btn-elimina');
            botonElimina.setAttribute('data-id', id); // Asigna el ID al botón de confirmación
        }


        function actualizaCantidad(cantidad, id) {
            if (cantidad <= 0 || isNaN(cantidad)) {
                alert("La cantidad debe ser mayor a 0.");
                return;
            }

            let url = 'clases/actualizar_carrito.php';
            let formData = new FormData();
            formData.append('action', 'agregar');
            formData.append('id', id);
            formData.append('cantidad', cantidad);

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors',
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.ok) {
                        // Actualiza el subtotal en el DOM.
                        const subtotalElement = document.getElementById(`subtotal_${id}`);
                        if (subtotalElement) {
                            subtotalElement.textContent = `$${data.sub}`;
                        }
                        actualizarTotal(); // Recalcula el total general.
                    } else {
                        alert(`Error al actualizar: ${data.error || 'Por favor, inténtalo de nuevo.'}`);
                    }
                })
                .catch((error) => {
                    console.error('Error en la solicitud:', error);
                    // alert('Error en la conexión. Por favor, inténtalo más tarde.');
                });
        }



        //NO FUNCIONA ELIMINAR 
        function eliminar() {
            let botonElimina = document.getElementById('btn-elimina');
            let id = botonElimina.getAttribute('data-id');

            let url = 'clases/actualizar_carrito.php';
            let formData = new FormData();
            formData.append('action', 'eliminar');
            formData.append('id', id);

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors',
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.ok) {
                        // Elimina la fila del producto del DOM.
                        const fila = document.querySelector(`#eliminar_${id}`).closest('tr');
                        if (fila) fila.remove();
                        actualizarTotal(); // Recalcula el total general.
                    } else {
                        alert(`Error al eliminar: ${data.error || 'Por favor, inténtalo de nuevo.'}`);
                    }
                })
                .catch((error) => {
                    console.error('Error en la solicitud:', error);
                    // alert('Error en la conexión. Por favor, inténtalo más tarde.');
                });
        }
    </script>


</body>

</html>