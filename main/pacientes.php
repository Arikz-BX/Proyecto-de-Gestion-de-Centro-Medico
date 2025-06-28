<?php
session_start();
if (!isset($_SESSION['nombreusuario'])) {
    header("Location: ../main/inicio-sesion.php"); 
    exit();
}
include('../acciones/registrar_pacientes.php');
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
    <?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
    <div class="container">
        <h1 class="paciente">Registro de Nuevo Paciente</h1>
        <form id="formularioPaciente" action="../acciones/registrar_pacientes.php" method="post">
            <div class="formulario">
                <p>Nombre Paciente: <input type="text" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$" required placeholder="Ingrese el Nombre" name="nombrepaciente" class="form-control"></p>
                <p>DNI: <input type="text" id="dni" minlength="8" pattern="^\d{8}$" required placeholder="Ingrese el Documento" name="dni" class="form-control"></p>
                <p>Obra Social: <input type="text" id="obrasocial" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$" required placeholder="Ingrese la Obra Social" minlength="6" name="obrasocial" class="form-control"></p>
                <p>Direccion: <input type="text" id="direccion" pattern="^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s.,#-]*$" required placeholder="Ingrese Direccion" minlength="9" name="direccion" class="form-control"></p>
                <p>Telefono: <input type="text" id="telefono" pattern="^\+?\d{9,15}$" required placeholder="Ingrese Contacto Telefonico" minlength="9" maxlength="15" name="telefono" class="form-control"></p>
                <p>Correo Electronico: <input type="text" id="correoelectronico" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" placeholder="Ingrese Correo de Contacto" name="correoelectronico" class="form-control"></p>
                <label for="notas" class="form-control">Notas Adicionales:</label>
                <textarea id="notas" name="notas" pattern="^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s.,#-]*$" class="form-control"></textarea>      
                <button type="submit" name="guardar_paciente">Guardar Paciente</button>
            </div>
    </form>
       <!-- <a href="listado_pacientes.php">
                <button type="submit" class="button">Listado de Pacientes</button>
            </a> -->
</div>
<div class= footer>
        <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
<script>
    const form = document.getElementById('formularioPaciente');
    let formInicial = new FormData(form);

    window.addEventListener('DOMContentLoaded', () => {
        formInicial = new FormData(form);
    });

    function detectaCambios() {
        const formActual = new FormData(form);
        for (let [key, value] of formInicial.entries()) {
            if (formActual.get(key) !== value) {
                return true;
            }
        }
        return false;
    }

    const enlacesRetorno = document.querySelectorAll('a.boton-retorno');
    enlacesRetorno.forEach(enlace => {
        enlace.addEventListener('click', function (e) {
            if (detectaCambios()) {
                const confirmacion = confirm("¿Estás seguro de que deseas cancelar los cambios?");
                if (!confirmacion) {
                    e.preventDefault();
                }
            }
        });
    });
</script>
</body>
</html>