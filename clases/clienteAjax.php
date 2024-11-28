<?php

require_once '../config/database.php';

require_once 'clientesFunciones.php';

$data = [];

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action == 'usuarioExiste') {

        $db = new Database();
        $con = $db->conectar();
        $dato ['ok'] = usuarioExiste(($_POST['usuario']), $con);
    }
}



echo json_encode($data);
