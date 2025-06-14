<?php
session_start();
include('../conexion/conexionbasededatos.php');
$mensaje_error = "";
if (isset($_GET['error'])) {
    $error_code = $_GET['error'];

    // Se usa un SWITCH para cada caso de error.
    switch ($error_code) {
        case 'clave_incorrecta':
            $mensaje_error = "ERROR: Contraseña incorrecta. Por favor, inténtelo de nuevo.";
            break;
        case 'usuario_no_encontrado':
            $mensaje_error = "ERROR: Usuario no encontrado. Verifique su nombre de usuario.";
            break;
        case 'usuario_inactivo':
            $mensaje_error = "ERROR: Su cuenta está inactiva. Por favor, contacte al administrador.";
            break;
        case 'tipo_usuario_desconocido':
            $mensaje_error = "ERROR: Tipo de usuario desconocido. Contacte al administrador.";
            break;
        case 'error_consulta_db':
            $mensaje_error = "ERROR: Hubo un problema con la base de datos. Inténtelo más tarde.";
            break;
        default:
            $mensaje_error = "ERROR: Algo salió mal. Por favor, inténtelo de nuevo.";
            break;
    }
    echo "<script>";
    echo "alert('" . htmlspecialchars($mensaje_error, ENT_QUOTES, 'UTF-8'). "');"; //Linea cambiada a peticion de los Profesores, caja de alerta para el error.
    echo "window.history.replaceState({}, document.title, window.location.pathname);"; //Linea de Codigo usada para que el ALERT no se muestre al recargar la misma pagina.
    echo "</script>";
    //{}: Es el objeto state (en este caso, vacío). document.title: Es el título de la página. window.location.pathname: Le dice al navegador que reemplace la URL actual con la ruta del archivo (ejemplo, /main/inicio-sesion.php), eliminando cualquier parámetro GET (?error=...)//      
}
//Ahora se usa GET para conseguir el error, el uso de GET en este caso esta bien porque no maneja informacion de la base de datos si no que solo el error para mostrar el mensaje.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesion</title>
    <link rel="stylesheet" href="../estilos/estilologin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="icon" href="../estilos/usuarios.ico">
</head>
<body>
<form action="../acciones/loginreg/verificar.php" method="post">
    <div class="formulario">
        <h1>Inicio de Sesion</h1>
        <!--// Mostrar el mensaje de error si existe, ACTUALIZADO: 5/6/25 cambiado a caja ALERT.-->
        <div class="username">
            <p>Usuario <input type="text" required placeholder="Ingrese su Nombre" name="usuario"></p>
        </div>
        <div class="password">
            <p>Clave <input type="password" minlength="8" required placeholder="Ingrese su Clave" name="clave"></p>
        </div>
        <input type="submit" value="Ingresar"> 
    </div>
</form>
    <div class= footer>
        <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
    </div>
</body>
</html>