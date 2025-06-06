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
    <title>Gestor Secretario</title>
    <link rel="stylesheet" href="../estilos/estiloindex.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1>Panel de Secretario</h1>
        <div class="dashboard-icons">
                <a href="usuarios.php" class="dashboard-icon">
                   <img src="../estilos/usuarioscheck.ico" alt="Usuarios"> <p>Usuarios</p>
                </a>
                <a href="medicos.php" class="dashboard-icon">
                   <img src="../estilos/medicos.ico" alt="Médicos"> <p>Medicos</p>
                </a>
                <a href="listado_pacientes.php" class="dashboard-icon">
                   <img src="../estilos/medicolista.ico" alt="Pacientes"> <p>Pacientes</p>
                </a>
                <a href="turnos.php" class="dashboard-icon">
                   <img src="../estilos/medicosturnos.ico" alt="Turnos"> <p>Turnos</p>
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