<?php


function esNulo(array $parametro)
{
    foreach ($parametro as $parametro) {
        if (strlen(trim($parametro)) < 1) {
            return true;
        }
    }
}


function validaPassword($password, $repassword)
{
    if (strcmp($password, $repassword) === 0) {
        return false;
    }
    return true;
}

function esEmail($email)
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    }
    return false;
}

function generarToken()
{
    return md5(uniqid(mt_rand(), false));
}


function usuarioExiste($usuario, $con)
{

    $sql = $con->prepare("SELECT id FROM usuarios WHERE usuario  LIKE ? LIMIT 1");
    $sql->execute([$usuario]);
    if ($sql->fetchColumn() > 0) {
        return true;
    }
    return false;
}



function emailExiste($email, $con)
{

    $sql = $con->prepare("SELECT id FROM clientes WHERE email  LIKE ? LIMIT 1");
    $sql->execute([$email]);
    if ($sql->fetchColumn() > 0) {
        return true;
    }
    return false;
}



function registraCliente(array $datos, $con)
{

    $sql = $con->prepare("INSERT INTO clientes (nombres, apellidos, email, telefono,
     dni, estatus, fecha_alta) VALUES (?,?,?,?,?,1,(now()))");
    if ($sql->execute($datos)) {
        return $con->lastInsertId();
    }

    return 0;
}

function registraUsuario(array $datos, $con)
{
    $sql = $con->prepare("INSERT INTO usuarios (usuario, password, token, id_cliente,activacion) 
    VALUES (?,?,?,?,1)");
    if ($sql->execute($datos)) {

        return true;
    }
    return false;
}



function mostrarMensajes(array $error)
{
    if (count($error) > 0) {
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">';

        foreach ($error as $error) {
            echo '<li> ' . $error . '</li>';
        }
        echo '<ul>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}


function login($usuario, $password, $con) {
    $sql = $con->prepare("SELECT id, usuario, password FROM usuarios WHERE usuario LIKE ? LIMIT 1");
    $sql->execute([$usuario]);
    if ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        if (esActivo($usuario, $con)) {
            // Comparación sin cifrado (solo temporal)
            if ($password === $row['password']) { 
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['usuario'];
                header("location: index.php");
                exit;
            }
        } else {
            return 'El usuario no ha sido activado';
        }
    }
    return 'Usuario o contraseña incorrecta';
}

function esActivo($usuario, $con) {
    $sql = $con->prepare("SELECT activacion FROM usuarios WHERE usuario LIKE ? LIMIT 1");
    $sql->execute([$usuario]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row && $row['activacion'] == 1;
}

