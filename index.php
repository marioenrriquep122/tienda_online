<?php
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT id, nombre, precio FROM productos WHERE activo = 1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
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
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
            <?php foreach ($resultado as $row) { ?>

                <!-- Producto -->
                <div class="col">
                    <div class="card shadow-sm h-100"> <!-- h-100 asegura que todas las tarjetas tengan el mismo alto -->

                        <?php
                        $id = $row['id'];
                        $imagen = "images/productos/" . $id . "/principal.jpg";

                        if (!file_exists($imagen)) {
                            $imagen = "images/no-photo.jpg";
                        }
                        ?>
                        <img src="<?php echo $imagen; ?>" class="img-fluid mx-auto d-block" style="height: 200px; width: 100%; object-fit: contain;"> <!-- Ajusta la imagen uniformemente -->
                        
                        <div class="card-body d-flex flex-column"> <!-- d-flex para organizar el contenido -->
                            <h5 class="card-title"><?php echo $row['nombre']; ?></h5>
                            <p class="card-text">$<?php echo number_format($row['precio'], 2, '.', ','); ?></p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <a href="details.php?id=<?php echo $row['id']; ?>&token=<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>" class="btn btn-primary">Detalles</a>
                                </div>
                                <button class="btn btn-outline-success" type="button"
                                    onclick="addProducto(<?php echo $row['id']; ?>, '<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>')">
                                    Agregar al carrito
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

            <?php } ?>
        </div>
    </div>
</main>







    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        function addProducto(id, token) {
            let url = 'clases/carrito.php'
            let formData = new FormData()
            formData.append('id', id)
            formData.append('token', token)

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors'
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        let element = document.getElementById("num_cart")
                        element.innerHTML = data.numero

                    }
                })

        }
    </script>


</body>

</html>