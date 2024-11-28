<?php
require 'config/config.php';
require 'config/database.php';
require 'clases/clientesFunciones.php';


$db = new Database();
$con = $db->conectar();






$error = [];

if (!empty($_POST)) {
    
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    

    if (esNulo([$usuario, $password])) {
        $error[] = 'Todos los campos son obligatorios';
    }


    if(count($error) == 0){
        $error[] = login($usuario, $password, $con);
        
    }
    

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

    <main class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg border-0 rounded-4" style="width: 100%; max-width: 400px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h2 class="card-title fw-bold text-primary mb-3">Iniciar Sesión</h2>
                <p class="text-muted">Bienvenido de nuevo, ingresa tus credenciales</p>
            </div>

            <?php mostrarMensajes($error); ?>

            <form action="login.php" method="post" autocomplete="off">
                <div class="form-floating mb-3">
                    <input class="form-control rounded-3 shadow-sm" type="text" name="usuario" id="usuario" placeholder="Usuario" required>
                    <label for="usuario" class="text-muted">
                        <i class="bi bi-person-fill me-2"></i>Usuario
                    </label>
                </div>

                <div class="form-floating mb-3">
                    <input class="form-control rounded-3 shadow-sm" type="password" name="password" id="password" placeholder="Contraseña" required>
                    <label for="password" class="text-muted">
                        <i class="bi bi-lock-fill me-2"></i>Contraseña
                    </label>
                    <div class="form-text">
                        <a href="recupera.php" class="text-decoration-none text-secondary small">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg rounded-3 shadow">
                        Ingresar
                        <i class="bi bi-arrow-right-circle ms-2"></i>
                    </button>
                </div>

                <div class="text-center">
                    <hr class="my-4">
                    <p class="text-muted">
                        ¿No tienes cuenta? 
                        <a href="registro.php" class="fw-bold text-decoration-none text-primary">
                            Regístrate aquí
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    



</body>

</html>