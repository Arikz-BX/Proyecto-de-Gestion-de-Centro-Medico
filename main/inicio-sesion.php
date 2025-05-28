<?php
session_start();
include('../conexion/conexionbasededatos.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesion</title>
    <link rel="stylesheet" href="../estilos/estilologin.css">
    <link rel="icon" href="../estilos/usuarios.ico">
</head>
<body>
    <form action="../acciones/loginreg/verificar.php" method="post">
    <div class="formulario">
        <h1>Inicio de Sesion</h1>
        <div class="username">
            <p>Usuario <input type="text" required placeholder="Ingrese su Nombre" name="usuario"></p>
        </div>
        <div class="password">
            <p>Clave <input type="password" minlength="8" required placeholder="Ingrese su Clave" name="clave"></p>
        </div>
    <input type="submit" value="Ingresar">
</div>
</body>
</html>