<?php
session_start();
if (!isset($_SESSION['nombreusuario'])) {
    header("Location: ../main/inicio-sesion.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor Administrativo</title>
    <link rel="stylesheet" href="../estilos/estiloindex.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
<?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
    <div class="container">
        <h1>Panel de Administrador</h1>
        <div class="dashboard-icons">
            <a href="usuarios.php" class="dashboard-icon">
                <img src="../estilos/usuarioscheck.ico" alt="Usuarios"> <p>Usuarios</p>
            </a>
            <a href="medicos.php" class="dashboard-icon">
                <img src="../estilos/medicos.ico" alt="Médicos"> <p>Médicos</p>
            </a>   
            <!--<a href="agenda.php" class="dashboard-icon">
                <img src="../estilos/agenda.ico" alt="Médicos"> <p>Agenda</p>
            </a>-->
            <a href="turnos.php" class="dashboard-icon">
               <img src="../estilos/medicosturnos.ico" alt="Turnos"> <p>Turnos</p>
            </a>
            <a href="listado_pacientes.php" class="dashboard-icon">
               <img src="../estilos/medicolista.ico" alt="Pacientes"> <p>Pacientes</p>
            </a>
        </div>
    </div>
   <div class= footer>
        <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
   </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>