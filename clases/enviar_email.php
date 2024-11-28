<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/SMTP.php';



$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'marioenrriquep122@gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'marioenrriquep122@gmail.com';                     //SMTP username
    $mail->Password   = '123';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('marioenrriquep122@gmail.com', 'TIENDA ONLINE');
    $mail->addAddress('tiendaonline@gmail.com', 'Joe User');     //Add a recipient
    

  

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Detalle de su compra';

    $cuerpo = '<h1>Gracias por su compra</h1>';
    $cuerpo .= '<p>el id de su compra es <b> ' . $idTrransaccion . '</b></p>';


    $mail->Body    = $cuerpo;
    $mail->AltBody = 'Le enviamos los detalles de su compra';



    $mail->setLanguage('es','../phpmailer/language/phpmailer.lang-es.php');
    $mail->send();


    
} catch (Exception $e) {
    echo "Error al enviar el correo electronico: {$mail->ErrorInfo}";
}



?>