<?php
session_start();
include('../conexion/conexionbasededatos.php');
if (!isset($_SESSION['nombreusuario'])) {
    header("Location: ../main/inicio-sesion.php"); // Redirige a la página de inicio de sesión si no hay sesión
    exit();
}

function obtenerListaDeUsuarios($enlace) {
    $sql = "SELECT idusuario, nombreusuario, nombrecompleto, tipousuario, estado FROM usuarios";
    $resultado = $enlace->query($sql);
    return $resultado;
}

$mensaje_toast = '';
$tipo_toast = '';

if (isset($_GET['success'])) {
    $codigo_efectivo = $_GET['success'];
    switch ($codigo_efectivo) {
        case 'usuario_inactivado':
            $mensaje_toast = '¡Usuario inactivado correctamente!';
            $tipo_toast = 'success';
            break;
        case 'usuario_reactivado':
            $mensaje_toast = '¡Usuario reactivado correctamente!';
            $tipo_toast = 'success';
            break;
        case 'usuario_modificado':
            $mensaje_toast = '¡Usuario modificado correctamente!';
            $tipo_toast = 'success';
            break;
        case 'usuario_registrado':
            $mensaje_toast = '¡Usuario registrado correctamente!';
            $tipo_toast = 'success';
            break;
        default:
            $mensaje_toast = '¡Operacion exitosa!';
            $tipo_toast = 'success';
    }
} elseif (isset($_GET['error'])) {
    $codigo_error = $_GET['error'];
    switch ($codigo_error) {
        case 'fallo_usuario_inactivado':
            $mensaje_toast = 'Error al desactivar al usuario.';
            $tipo_toast = 'danger';
            break;
        case 'fallo_usuario_modificado':
            $mensaje_toast = 'Error al modificar al usuario.';
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
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link rel="icon" href="../estilos/usuarios.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>
<?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
<div class="container">
        <h1>Gestión de Usuarios</h1>
        <div class="lista-usuarios">
            <h2>Lista de Usuarios</h2>
            <?php
            $resultado_usuarios = obtenerListaDeUsuarios($enlace);
            if ($resultado_usuarios->num_rows > 0) {
                echo "<table>
                        <thead>
                            <tr>
                                <th>ID Usuario</th>
                                <th>Nombre de Usuario</th>
                                <th>Nombre completo</th>
                                <th>Tipo de Usuario</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>";
                        while ($fila_usuario = $resultado_usuarios->fetch_assoc()) {
                            // Sanitiza los datos para evitar XSS al mostrarlos en HTML
                        $id        = htmlspecialchars($fila_usuario['idusuario'], ENT_QUOTES, 'UTF-8');
                        $nombre_usuario    = htmlspecialchars($fila_usuario['nombreusuario'], ENT_QUOTES, 'UTF-8');
                        $nombre_personal    = htmlspecialchars($fila_usuario['nombrecompleto'], ENT_QUOTES, 'UTF-8');
                        $tipo     = htmlspecialchars($fila_usuario['tipousuario'], ENT_QUOTES, 'UTF-8');
                        $estado_usuario = htmlspecialchars($fila_usuario['estado'], ENT_QUOTES, 'UTF-8');
                        echo "<tr>";
                        echo "<td>". $id ."</td>"; //Celda del ID en los Usuarios
                        echo "<td>". $nombre_usuario . "</td>"; //Celda del Nombre de Usuario
                        echo "<td>". $nombre_personal . "</td>"; //Celda del Nombre de Usuario
                        echo "<td>". $tipo ."</td>"; //Celda del tipousuario
                        $clase_badge_estado = '';
                        if($estado_usuario == 'Activo'){
                            $clase_badge_estado = 'text-bg-success';
                        } elseif ($estado_usuario == 'Inactivo') {
                            $clase_badge_estado = 'text-bg-danger';
                        } else {
                            $clase_badge_estado = 'text-bg-secondary';
                        }
                        echo '<td><span class="badge '. $clase_badge_estado . '">' . $estado_usuario . '</span></td>';
                        echo "<td>"; //Celda de Acciones
                        echo "<form action='modificar-usuario.php' method='post' style='display:inline-block; margin-right: 5px;'>";
                        echo "<input type='hidden' name='id' value='". $id ."'>";
                        // if($fila_usuario['tipousuario'] != 'Administrador'){
                        echo "<button type='submit' class='boton-modificar'>Modificar</button>";
                        echo "</form>"; 
                        //}
                        if ($fila_usuario["nombreusuario"] != $_SESSION['nombreusuario']) { //Previene que se borre a si mismo el Secretario/Administrador.
                            if ($estado_usuario == 'Activo'){
                                echo "<form action='../acciones/usuarios/eliminar_usuario.php'  method='post' style='display:inline' onsubmit='return confirm(\"¿Estás seguro de que deseas dar de baja a este usuario?\")'>";
                                echo "<input type='hidden' name='idusuario' value='". htmlspecialchars($id) ."'>";
                                echo "<button type='submit'>Dar de Baja</button>";
                                echo "</form>";
                            } elseif ($estado_usuario == 'Inactivo') {
                                echo "<form action='../acciones/usuarios/reactivar_usuario.php'  method='post' style='display:inline' onsubmit='return confirm(\"¿Estás seguro de que deseas reactivar a este usuario?\")'>";
                                echo "<input type='hidden' name='idusuario' value='". htmlspecialchars($id) ."'>";
                                echo "<button type='submit'>Dar de Alta</button>";
                                echo "</form>";
                            }
                        }
                        echo "</td>";
                        echo "</tr>";
            }
                echo "</tbody>
                     </table>";
            } else {
                echo "<p>No hay usuarios registrados.</p>";
            }
            ?>
            <?php if ($_SESSION['tipousuario'] == 'Administrador' || $_SESSION['tipousuario'] == 'Secretario') { ?>
            <a href="registro-sesion.php">Registrar Nuevo Usuario</a>
            <?php } else { ?>
            <p>No tienes permiso para modificar esta tabla.</p> 
            <?php } ?>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast align-items-center text-white bg-<?php echo $tipo_toast; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php echo htmlspecialchars($mensaje_toast); ?>
                </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
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
</div> <!-- 9/6 Necesario para dividir y que se vea bien el Footer -->
<div class= footer>
    <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>