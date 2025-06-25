<?php
session_start();
if (!isset($_SESSION['nombreusuario'])) {
    header("Location: ../main/inicio-sesion.php");
    exit();
}
$mensaje_toast = '';
$tipo_toast = '';

if (isset($_GET['success'])) {
    $codigo_efectivo = $_GET['success'];
    switch ($codigo_efectivo) {
        case 'usuario_modificado':
            $mensaje_toast = '¡Usuario modificado correctamente!';
            $tipo_toast = 'success';
            break;
        default:
            $mensaje_toast = '¡Operacion exitosa!';
            $tipo_toast = 'success';
    }
} elseif (isset($_GET['error'])) {
    $codigo_error = $_GET['error'];
    switch ($codigo_error) {
        case 'usuario_modificar_denegado':
            $mensaje_toast = 'No puedes modificar otros usuarios.';
            $tipo_toast = 'danger';
            break;
        case 'id_usuario_invalido':
            $mensaje_toast = 'Error al cargar el ID.';
            $tipo_toast = 'danger';
            break;    
        default:
            $mensaje_toast = 'Ocurrio un error inesperado.';
            $tipo_toast = 'danger';
    }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Medicos</title>
    <link rel="stylesheet" href="../estilos/estiloindex.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
<?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
    <div class="container">
        <h1>Panel de Medico</h1>
        <div class="dashboard-icons">
            <!-- <a href="agenda.php" class="dashboard-icon">
                <img src="../estilos/agenda.ico" alt="Médicos"> <p>Agenda</p>
            </a> -->
            <a href="modificar-usuario.php" class="dashboard-icon">
                   <img src="../estilos/usuarioscheck.ico" alt="Perfil"> <p>Mi Perfil</p>
            </a>
            <a href="listado_pacientes.php" class="dashboard-icon">
                   <img src="../estilos/medicolista.ico" alt="Pacientes"> <p>Pacientes</p>
            </a>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // PHP pasa el mensaje a JavaScript
            const mensajeToast = "<?php echo addslashes($mensaje_toast); ?>";
               
            if (mensajeToast) {
                const toastLiveExample = document.getElementById('liveToast');
                const toast = new bootstrap.Toast(toastLiveExample);
                toast.show();

                // Limpiar la URL después de mostrar el mensaje
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script> 
    <div class= footer>
        <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2>        
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>