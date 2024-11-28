<?php
require 'config/config.php';
require 'config/database.php';

// Conexión a la base de datos
$db = new Database();
$con = $db->conectar();

$error = [];
$success = '';

// Procesar el formulario
if (!empty($_POST)) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $asunto = trim($_POST['asunto']);
    $mensaje = trim($_POST['mensaje']);

    // Validación de campos
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
        $error[] = 'Todos los campos son obligatorios.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = 'El correo electrónico no es válido.';
    }

    // Si no hay errores, guardar en la base de datos
    if (count($error) === 0) {
        $sql = "INSERT INTO reporte (nombre, email, asunto, mensaje, fecha) VALUES (:nombre, :email, :asunto, :mensaje, NOW())";
        $stmt = $con->prepare($sql);

        try {
            $stmt->bindValue(':nombre', $nombre);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':asunto', $asunto);
            $stmt->bindValue(':mensaje', $mensaje);

            if ($stmt->execute()) {
                echo "<script>
                    alert('Su reporte ha sido enviado. Nos contactaremos con usted en un plazo máximo de 24 horas.');
                    window.location.href = 'contacto.php';
                </script>";
                exit;
            } else {
                $error[] = 'Error al enviar el mensaje. Inténtalo de nuevo más tarde.';
            }
        } catch (PDOException $e) {
            $error[] = 'Error en la base de datos: ' . $e->getMessage();
        }
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
    <title>Contacto - Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <div class="navbar navbar-expand-lg navbar-dark bg-dark">
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
    </header>

    <main class="container py-5" style="background-color: #f4f6f9;">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-4" style="background-color: #ffffff; border: 1px solid #e9ecef;">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="card-title fw-bold text-primary mb-3" style="color: #2c3e50;">Contacto</h2>
                        <p class="text-muted" style="color: #6c757d;">¿Tienes algún problema con un producto o una pregunta? 
                            Llena el formulario y nos pondremos en contacto contigo.</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: #f8d7da; border-color: #f5c6cb; color: #721c24;">
                            <?php foreach ($error as $err): ?>
                                <p class="mb-1">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <?php echo $err; ?>
                                </p>
                            <?php endforeach; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="contacto.php" method="POST">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" name="nombre" id="nombre" 
                                           class="form-control rounded-3 shadow-sm" 
                                           placeholder="Tu nombre" required
                                           style="background-color: #f1f3f5; border-color: #ced4da;">
                                    <label for="nombre" class="text-muted">
                                        <i class="bi bi-person-fill me-2"></i>Nombre
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="email" name="email" id="email" 
                                           class="form-control rounded-3 shadow-sm" 
                                           placeholder="correo@ejemplo.com" required
                                           style="background-color: #f1f3f5; border-color: #ced4da;">
                                    <label for="email" class="text-muted">
                                        <i class="bi bi-envelope-fill me-2"></i>Correo Electrónico
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select name="asunto" id="asunto" 
                                            class="form-select rounded-3 shadow-sm" required
                                            style="background-color: #f1f3f5; border-color: #ced4da;">
                                        <option value="" disabled selected>Selecciona una opción</option>
                                        <option value="Reporte de un producto">Reporte de un producto</option>
                                        <option value="Reporte: No me llega el producto">Reporte: No me llega el producto</option>
                                        <option value="Reporte: Me cobraron doble">Reporte: Me cobraron doble</option>
                                        <option value="Consulta: ¿Puedo comprar grandes cantidades?">Consulta: ¿Puedo comprar grandes cantidades?</option>
                                    </select>
                                    <label for="asunto" class="text-muted">
                                        <i class="bi bi-chat-square-text-fill me-2"></i>Asunto
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea name="mensaje" id="mensaje" 
                                              class="form-control rounded-3 shadow-sm" 
                                              placeholder="Escribe tu mensaje aquí" 
                                              style="height: 150px; background-color: #f1f3f5; border-color: #ced4da;" required></textarea>
                                    <label for="mensaje" class="text-muted">
                                        <i class="bi bi-pencil-fill me-2"></i>Mensaje
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-3 shadow" 
                                            style="background-color: #3498db; border-color: #2980b9;">
                                        Enviar Mensaje 
                                        <i class="bi bi-send-fill ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>