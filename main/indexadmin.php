<?php
session_start();
if (!isset($_SESSION['nombreusuario'])) {
    header("Location: ../main/inicio-sesion.php"); 
    exit();
}
if (isset($_POST['logout'])) {
   $_SESSION = array();
   if (ini_get("session.use_cookies")) {
       $params = session_get_cookie_params();
       setcookie(session_name(), '', time() - 42000,
           $params["path"], $params["domain"],
           $params["secure"], $params["httponly"]
       );
   }
   session_destroy();
   // Redirige al usuario a la página de inicio de sesión
   header("Location: inicio-sesion.php");
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
</head>
<body>
    <div class="container">
        <h1>Panel de Administrador</h1>
        <div class="dashboard-icons">
            <div class="dashboard-icon">
                <a href="usuarios.php">
                   <img src="../estilos/usuarioscheck.ico" alt="Usuarios"> <p>Usuarios</p>
                </a>
                <a href="medicos.php">
                   <img src="../estilos/medicos.ico" alt="Médicos"></i> <p>Medicos</p>
                </a>
                <div class="dashboard-icon">
                <a href="agenda.php">
                   <img src="../estilos/agenda.ico" alt="Médicos"></i> <p>Agenda</p>
                </a>
                <div class="dashboard-icon">
                </a>
                <div class="dashboard-icon">
                <a href="turnos.php">
                   <img src="../estilos/medicosturnos.ico" alt="Turnos"></i> <p>Turnos</p>
                </a>
                <div class="dashboard-icon">
                <a href="listado_pacientes.php">
                   <img src="../estilos/medicolista.ico" alt="Pacientes"></i> <p>Pacientes</p>
                </a>
                <div class="dashboard-icon">
    </div>
   <form method="post" style="text-align: right;">
   <button type="submit" method="post" name="logout" id="logout">Cerrar Sesion</button>
   </form>
</body>
</html>