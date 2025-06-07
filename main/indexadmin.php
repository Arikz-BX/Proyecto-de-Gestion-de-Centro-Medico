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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<header class="d-flex justify-content-end p-3 bg-light border-bottom">
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            Usuario: <?php echo htmlspecialchars($_SESSION['nombreusuario']); ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
            <li><h6 class="dropdown-header">Hola, <?php echo htmlspecialchars($_SESSION['nombreusuario']); ?>!</h6></li>

            <li><a class="dropdown-item" href="index.php">Mi Dashboard (General)</a></li>

            <?php if ($_SESSION['tipousuario'] == 'Secretario' || $_SESSION['tipousuario'] == 'Administrador') { ?>
                <li><a class="dropdown-item" href="usuarios.php">Gestión de Usuarios</a></li>
                <li><a class="dropdown-item" href="medicos.php">Gestión de Médicos</a></li>
                <li><a class="dropdown-item" href="pacientes.php">Gestión de Pacientes</a></li>
                <li><a class="dropdown-item" href="turnos.php">Gestión de Turnos</a></li>
            <?php } ?>

            <?php if ($_SESSION['tipousuario'] == 'Administrador') { ?>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Navegación Admin:</h6></li>
                <li><a class="dropdown-item" href="indexadmin.php">Dashboard Administrador</a></li>
                <li><a class="dropdown-item" href="indexsecretario.php">Dashboard Secretario (vista)</a></li>
                <?php } ?>

            <li><hr class="dropdown-divider"></li>

            <li><a class="dropdown-item" href="../acciones/cerrar_sesion.php">Cerrar Sesión</a></li>
        </ul>
    </div>
</header>
<body>
    <div class="container">
        <h1>Panel de Administrador</h1>
        <div class="dashboard-icons">
            <a href="usuarios.php" class="dashboard-icon">
                <img src="../estilos/usuarioscheck.ico" alt="Usuarios"> <p>Usuarios</p>
            </a>
            <a href="medicos.php" class="dashboard-icon">
                <img src="../estilos/medicos.ico" alt="Médicos"> <p>Medicos</p>
            </a>   
            <a href="agenda.php" class="dashboard-icon">
                <img src="../estilos/agenda.ico" alt="Médicos"> <p>Agenda</p>
            </a>
            <a href="turnos.php" class="dashboard-icon">
               <img src="../estilos/medicosturnos.ico" alt="Turnos"> <p>Turnos</p>
            </a>
            <a href="listado_pacientes.php" class="dashboard-icon">
               <img src="../estilos/medicolista.ico" alt="Pacientes"> <p>Pacientes</p>
            </a>
        </div>
    </div>
   <form method="post" style="text-align: right;">
   <button type="submit" method="post" name="logout" id="logout">Cerrar Sesion</button>
   </form>
   <div class= footer>
        <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico<h2> 
   </div>
</body>
</html>