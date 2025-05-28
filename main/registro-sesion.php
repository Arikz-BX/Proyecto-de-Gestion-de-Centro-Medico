<?php
session_start();
include('../conexion/conexionbasededatos.php');
function generarBotonRetorno() {
    if($_SESSION['tipousuario'] == 'Administrador' || $_SESSION['tipousuario'] == 'Secretario'){
        echo '<button onclick="window.location.href=\'usuarios.php\'">Ya registre una cuenta.</button>';
    }else{
        echo '<button onclick="window.location.href=\'inicio-sesion.php\'">Ya registre una cuenta.</button>';
    }
    
}
?>
<script>
function mostrarMatricula() {
    document.getElementById("matricula").style.display = "block";
  }
  
function ocultarMatricula() {
    document.getElementById("matricula").style.display = "none";
  }
</script>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Sesion</title>
    <link rel="stylesheet" href="../estilos/estilologin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="icon" href="../estilos/usuarioscheck.ico">
</head>
<body>
    <form action="../acciones/loginreg/registrar.php" method="post">
    <div class="formulario">
        <h1>Registro de Sesion</h1>
        <div class="username">
            <p>Usuario <input type="text" required placeholder="Ingrese su Nombre" name="usuario"></p>
        </div>
        <div class="password">
            <p>Clave <input type="password" maxlength="8" required placeholder="Ingrese su Clave" name="clave"></p>
        </div>
        <div class="form-group">
            <p>Tipo de Usuario:</p>
            <input type="radio" name="tipousuario" value="Medico" id="medico" required checked onclick="mostrarMatricula()"> Médico<br>
            <?php if (isset($_SESSION['tipousuario']) && $_SESSION['tipousuario'] == 'Administrador') {
                echo '<input type="radio" name="tipousuario" value="Secretario" id="secretario" required onclick="ocultarMatricula()"> Secretario<br>';
            }
            ?> 
        </div>
        <div class="form-group" id="matricula">
        <p>Matricula: <input type="id" required placeholder="Ingrese la Matricula" name="matricula"></p>
        </div>
    <input type="submit" value="Registrar" name="registrar">
    </form>
    <button onclick="window.location.href='../main/usuarios.php'">Ya registre una cuenta.</button>
</div>
</body>
</html>