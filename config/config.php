<?php


define("CLIENTE_ID", "AWcKt6C7X2TuHf7gw6-GqBMKhele7UhMF7RIHSeiL2MHv829aiYgktTRj7FycPFxlPi4GzIXupugO63J");
define("CURRENCY","USD");

define("SITE_URL", "http://localhost/Tienda/");
define("KEY_TOKEN", "ABCDEF123456789");
define("MONEDA", "$");

define("MAIL_HOST", "smtp.gmail.com");
define("MAIL_USER", "marioenrriquep122@gmail.com");
define("MAIL_PASS", "mario123");
define("MAIL_NAME", "Tienda Online");
define("MAIL_PORT", 465);



session_start();


$num_cart = 0;
if(isset($_SESSION['carrito']['productos'])){
    $num_cart = count($_SESSION['carrito']['productos']);
}






?>