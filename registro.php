<?php
require 'config/config.php';
require 'config/database.php';
require 'clases/clientesFunciones.php';


$db = new Database();
$con = $db->conectar();






$error = [];

if (!empty($_POST)) {
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $dni = trim($_POST['dni']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if (esNulo([$nombres, $apellidos, $email, $telefono, $dni, $usuario, $password, $repassword])) {
        $error[] = 'Todos los campos son obligatorios';
    }


    if (!esEmail($email)) {
        $error[] = 'El correo no es valido';
    }

    // if (!validaPassword($password, $repassword)) {
    //     $error[] = 'Las contraseñas no son iguales';
    // }


    if (usuarioExiste($usuario, $con)) {
        $error[] = "El nombre de usuario  $usuario ya existe";
    }

    if (emailExiste($email, $con)) {
        $error[] = "El correo electronico $email ya existe";
    }





    //     if (count($error) == 0) {
    //         $id = registraCliente([$nombres, $apellidos, $email, $telefono, $dni], $con);
    //         if ($id > 0) {

    //             require 'clases/mailer.php';
    //             $mailer = new mailer();

    //             $token = generarToken();
    //             $url = SITE_URL . 'activa_cliente.php?id=' . $id . '&token=' . $token;

    //             $asunto = 'Activa tu cuenta en tienda online';
    //             $cuerpo = "Buenas dias, tardes, noches $nombres, <br>
    //             Para continuar con el proceso de registro es indispensable dar clic en el siguiente enlace: <br>
    //             <a href='$url'>Activar cuenta</a>";

    //             $pass_hash = password_hash($password, PASSWORD_DEFAULT);
    //             $token = generarToken();
    //             if (registraUsuario([$usuario, $pass_hash, $token, $id], $con)) {
    //                 if ($mailer->enviar_email($email, $asunto, $cuerpo)) {
    //                     echo "Para terminar el proceso de registro, siga las instrucciones que le hemos enviado al correo electrónico $email";
    //                     exit;
    //                 } else {
    //                     $error[] = 'Error al enviar el correo de activación.';
    //                 }
    //             } else {
    //                 $error[] = 'Error al registrar el usuario.';
    //             }
    //         } else {
    //             $error[] = 'Error al registrar el cliente.';
    //         }
    //     }
    // }





    if (count($error) == 0) {
        // Registrar al cliente
        $id = registraCliente([$nombres, $apellidos, $email, $telefono, $dni], $con);

        if ($id > 0) {
            // Generar token y URL de activación
            $token = generarToken();


            // Datos para el usuario

            $token = generarToken();

            // Registrar usuario
            if (registraUsuario([$usuario, $password, $token, $id], $con)) {
                // Redirigir a login.php con mensaje de alerta
                echo "<script>
                alert('El registro fue exitoso. Su cuenta ha sido creada correctamente.');
                window.location.href = 'login.php';
            </script>";
                exit;
            } else {
                $error[] = 'Error al registrar el usuario.';
            }
        } else {
            $error[] = 'Error al registrar el cliente.';
        }
    }

    // Manejar errores
    if (!empty($error)) {
        foreach ($error as $err) {
            echo "<div class='alert alert-danger'>$err</div>";
        }
    }
}




?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tienda Online</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

</head>

<body>



    <main>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h2 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>Registro de Cliente
                        </h2>
                    </div>
                    <div class="card-body p-4">
                        <?php mostrarMensajes($error); ?>

                        <form id="registroForm" action="registro.php" method="POST" autocomplete="off" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" 
                                               class="form-control" 
                                               id="nombres" 
                                               name="nombres" 
                                               placeholder="Ingrese sus nombres" 
                                               pattern="[A-Za-zÁ-ÿ\s]+" 
                                               required>
                                        <label for="nombres">
                                            <i class="fas fa-user text-muted me-2"></i>Nombres
                                        </label>
                                        <div class="invalid-feedback">
                                            Por favor ingrese nombres válidos
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" 
                                               class="form-control" 
                                               id="apellidos" 
                                               name="apellidos" 
                                               placeholder="Ingrese sus apellidos" 
                                               pattern="[A-Za-zÁ-ÿ\s]+" 
                                               required>
                                        <label for="apellidos">
                                            <i class="fas fa-user text-muted me-2"></i>Apellidos
                                        </label>
                                        <div class="invalid-feedback">
                                            Por favor ingrese apellidos válidos
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               placeholder="nombre@ejemplo.com" 
                                               required>
                                        <label for="email">
                                            <i class="fas fa-envelope text-muted me-2"></i>Correo Electrónico
                                        </label>
                                        <div class="invalid-feedback">
                                            Por favor ingrese un correo electrónico válido
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="tel" 
                                               class="form-control" 
                                               id="telefono" 
                                               name="telefono" 
                                               placeholder="+52 123 456 7890" 
                                               pattern="[\+]?[0-9]{10,14}" 
                                               required>
                                        <label for="telefono">
                                            <i class="fas fa-phone text-muted me-2"></i>Teléfono
                                        </label>
                                        <div class="invalid-feedback">
                                            Por favor ingrese un número de teléfono válido
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" 
                                               class="form-control" 
                                               id="dni" 
                                               name="dni" 
                                               placeholder="12345678" 
                                               pattern="[0-9]{8}" 
                                               required>
                                        <label for="dni">
                                            <i class="fas fa-id-card text-muted me-2"></i>DNI
                                        </label>
                                        <div class="invalid-feedback">
                                            Por favor ingrese un DNI válido de 8 dígitos
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" 
                                               class="form-control" 
                                               id="usuario" 
                                               name="usuario" 
                                               placeholder="Nombre de usuario" 
                                               minlength="4" 
                                               maxlength="20" 
                                               required>
                                        <label for="usuario">
                                            <i class="fas fa-user-circle text-muted me-2"></i>Usuario
                                        </label>
                                        <small id="validaUsuario" class="text-danger small position-absolute"></small>
                                        <div class="invalid-feedback">
                                            Por favor elija un nombre de usuario (4-20 caracteres)
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3 position-relative">
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Contraseña" 
                                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                                               required>
                                        <label for="password">
                                            <i class="fas fa-lock text-muted me-2"></i>Contraseña
                                        </label>
                                        <button type="button" 
                                                class="btn btn-outline-secondary position-absolute end-0 top-50 translate-middle-y me-2 border-0" 
                                                id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback">
                                            La contraseña debe tener 8+ caracteres, una mayúscula, una minúscula y un número
                                        </div>
                                        <small class="text-muted">
                                            Mínimo 8 caracteres, una mayúscula, una minúscula y un número
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" 
                                               class="form-control" 
                                               id="repassword" 
                                               name="repassword" 
                                               placeholder="Repetir Contraseña" 
                                               required>
                                        <label for="repassword">
                                            <i class="fas fa-lock text-muted me-2"></i>Repetir Contraseña
                                        </label>
                                        <div class="invalid-feedback">
                                            Las contraseñas no coinciden
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="terminos" 
                                               required>
                                        <label class="form-check-label" for="terminos">
                                            Acepto los términos y condiciones
                                        </label>
                                        <div class="invalid-feedback">
                                            Debe aceptar los términos y condiciones
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" 
                                        class="btn btn-primary btn-lg w-100 py-2 mt-3">
                                    <i class="fas fa-user-plus me-2"></i>Registrarse
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <p class="text-muted mb-0">
                            ¿Ya tienes una cuenta? 
                            <a href="login.php" class="text-primary">Inicia sesión</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>







    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        let txtusuario = document.getElementById('usuario')
        txtusuario.addEventListener('blur', function() {
            existeUsuario(txtusuario.value)
        }, false)

        function existeUsuario(usuario) {
            let url = "clases/clientesAjax.php"
            let formData = new FormData()
            formData.append('action', 'existeUsuario')
            formData.append("usuario", usuario)
            fetch(url, {
                    method: 'POST',
                    body: formData,
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        document.getElementById('usuario').value = ''
                        document.getElementById('validaUsuario').innerHTML = 'El usuario ya existe'
                    } else {
                        document.getElementById('validaUsuario').innerHTML = ''
                    }

                })


        }
    </script>



</body>

</html>