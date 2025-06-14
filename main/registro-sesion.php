<?php
session_start();
include('../conexion/conexionbasededatos.php');
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
    <?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
    <div class="formulario">
        <h1>Guardar Usuario</h1>
        <form action="../acciones/loginreg/registrar.php" method="post">
            <div class="username">
            <p>Usuario <input type="text" required placeholder="Ingrese un Usuario" name="usuario"></p>
            </div>
            <div class="password">
            <p>Clave <input type="password" maxlength="8" required placeholder="Ingrese una Clave" name="clave"></p>
            </div>
            <div class="nombrepersonal">
            <p>Nombre <input type="text" required placeholder="Ingrese el Nombre" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$" name="nombre"></p>
            </div>
            <p>Tipo de Usuario:</p>
                <input type="radio" name="tipousuario" value="Medico" id="medico" required checked onclick="manejarMatricula()"> Médico<br>
                <?php if (isset($_SESSION['tipousuario']) && $_SESSION['tipousuario'] == 'Administrador') {
                echo '<input type="radio" name="tipousuario" value="Secretario" id="secretario" required onclick="manejarMatricula()"> Secretario<br>';
                } ?> 
            <div class="matricula" id="matricula">
            <p>Matricula: <input type="text" maxlength="12" placeholder="Ingrese la Matricula" name="matricula" id="inputMatricula"></p>
            </div>
           <input type="submit" value="Registrar" name="registrar">
    </form>
        <button onclick="window.location.href='../main/usuarios.php'">Ya registre una cuenta.</button>
    </div>
<div class= footer>
        <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>