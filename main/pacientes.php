<?php
session_start();
if (!isset($_SESSION['nombreusuario'])) {
    header("Location: ../main/inicio-sesion.php"); 
    exit();
}
function generarBotonRetorno() {
    if (isset($_SESSION['tipousuario'])) { // Primero, verifica si la sesión está iniciada
        if ($_SESSION['tipousuario'] == 'Administrador') {
            echo '<button onclick="window.location.href=\'indexadmin.php\'">Volver al Inicio</button>';
        } elseif ($_SESSION['tipousuario'] == 'Secretario') {
            echo '<button onclick="window.location.href=\'indexsecretario.php\'">Volver al Inicio</button>';
        }
    }
}
include('../acciones/registrar_pacientes.php')
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Pacientes</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1>Registro de Nuevo Paciente</h1>
        <form action="../acciones/registrar_pacientes.php" method="post">
            <div class="formulario">
                <p>Nombre Paciente: <input type="text" required placeholder="Ingrese el Nombre" name="nombrepaciente"></p>
                <p>DNI: <input type="text" id="dni" minlength="8" required placeholder="Ingrese el Documento" name="dni"></p>
                <p>Obra Social: <input type="text" id="obrasocial" required placeholder="Ingrese la Obra Social" minlength="6" name="obrasocial"></p>
                <p>Direccion: <input type="text" id="direccion" required placeholder="Ingrese Direccion" minlength="9" name="direccion"></p>
                <p>Telefono: <input type="text" id="telefono" required placeholder="Ingrese Contacto Telefonico" minlength="9" name="telefono"></p>
                <p>Correo Electronico: <input type="text" id="correoelectronico" placeholder="Ingrese Correo de Contacto" name="correoelectronico"></p>
                <label for="notas">Notas Adicionales:</label>
                <textarea id="notas" name="notas"></textarea>      
                <button type="submit" name="guardar_paciente">Guardar Paciente</button>
            </div>
    </form>
            <a href="listado_pacientes.php" class="button">Listado de Pacientes</a>
            <?php generarBotonRetorno(); //Para el boton de Retorno que aplique a Secretarios y Administrador.?>
</div>
</body>
</html>