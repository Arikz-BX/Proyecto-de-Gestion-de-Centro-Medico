<?php
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    include("config.php");
    $enlace = mysqli_connect($server, $user, $password, $bd);
    if(!$enlace){
        die("Conexion no Establecida.". mysqli_connect_error());
    }
    // Simplifique el codigo, no funcionaba debido a la version del XAMPP del material que estaba viendo, ahora funciona correctamente.
?>

