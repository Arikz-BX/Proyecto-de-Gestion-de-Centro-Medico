<?php
session_start();
include('../conexion/conexionbasededatos.php');
//Removi el Boton de Retorno ya que, no es necesario su uso si los Secretarios y el Admin son los unicos que registran Usuarios.
?>
<script>
    //Tuve que cambiar las dos funciones por esta que es mas completa (ya que implemente required al campo de Matricula), ahora usando la misma funcion para los dos Radio se puede registrar ambos usuarios (antes solo registraba Medicos porque el required seguia afectando con el campo oculto)
function manejarMatricula(){
    const matriculaDiv = document.getElementById("matricula"); //Ahora se comparte entre los dos casos.
    const matriculaInput = document.getElementById("inputMatricula");
    const medicoRadio = document.getElementById("medico");

  if (medicoRadio.checked) {
    matriculaDiv.style.display = "block";
    matriculaInput.setAttribute("required", "required");
  } else {
    matriculaDiv.style.display = "none";
    matriculaInput.removeAttribute("required");
  }
}
document.addEventListener('DOMContentLoaded', manejarMatricula);
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
    <div class="formulario">
        <h1>Registro de Sesion</h1>
        <form action="../acciones/loginreg/registrar.php" method="post">
            <div class="username">
            <p>Usuario <input type="text" required placeholder="Ingrese su Nombre" name="usuario"></p>
            </div>
            <div class="password">
            <p>Clave <input type="password" maxlength="8" required placeholder="Ingrese su Clave" name="clave"></p>
            </div>
            <div class="form-group">
            <p>Tipo de Usuario:</p>
                <input type="radio" name="tipousuario" value="Medico" id="medico" required checked onclick="manejarMatricula()"> Médico<br>
                <?php if (isset($_SESSION['tipousuario']) && $_SESSION['tipousuario'] == 'Administrador') {
                echo '<input type="radio" name="tipousuario" value="Secretario" id="secretario" required onclick="manejarMatricula()"> Secretario<br>';
                } ?> 
            </div>
            <div class="form-group" id="matricula">
            <p>Matricula: <input type="text" maxlength="12" placeholder="Ingrese la Matricula" name="matricula" id="inputMatricula"></p>
            </div>
           <input type="submit" value="Registrar" name="registrar">
    </form>
        <button onclick="window.location.href='../main/usuarios.php'">Ya registre una cuenta.</button>
    </div>
</body>
</html>