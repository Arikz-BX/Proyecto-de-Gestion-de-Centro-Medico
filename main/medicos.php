<?php
session_start();
include('../conexion/conexionbasededatos.php'); 
if (!isset($_SESSION['nombreusuario'])) {
    header("Location: ../main/inicio-sesion.php"); // Redirige a la página de inicio de sesión si no hay sesión
    exit();
}

$mensaje_toast = '';
$tipo_toast = '';

if (isset($_GET['success'])) {
    $codigo_efectivo = $_GET['success'];
    switch ($codigo_efectivo) {
        case 'medico_inactivado':
            $mensaje_toast = '¡Medico inactivado correctamente!';
            $tipo_toast = 'success';
            break;
        case 'medico_modificado':
            $mensaje_toast = '¡Medico modificado correctamente!';
            $tipo_toast = 'success';
            break;
        case 'medico_registrado':
            $mensaje_toast = '¡Medico registrado correctamente!';
            $tipo_toast = 'success';
            break;
        default:
            $mensaje_toast = '¡Operacion exitosa!';
            $tipo_toast = 'success';
    }
} elseif (isset($_GET['error'])) {
    $codigo_error = $_GET['error'];
    switch ($codigo_error) {
        case 'error_al_dar_de_baja':
            $mensaje_toast = 'Error al desactivar al Medico.';
            $tipo_toast = 'danger';
            break;
        case 'fallo_medico_modificado':
            $mensaje_toast = 'Error al modificar al Medico.';
            $tipo_toast = 'danger';
            break;
        case 'id_medico_invalido':
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
    <title>Listado de Medicos</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="icon" href="../estilos/medicos.ico">
</head>
<body>
    <?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
    <div class="container">
        <h1 class="medico">Gestión de Medicos</h1>
        <div class="lista-medicos">
            <h2>Listado de Medicos</h2>
            <table>
            <thead>
                <tr>
                    <th>ID Medico</th>
                    <th>Nombre Completo</th>
                    <th>DNI</th>
                    <th>Matricula</th>
                    <th>Consultorio</th>
                    <th>Direccion de Domicilio</th>
                    <th>Telefono</th>
                    <th>Correo</th>
                    <th>Especialidad</th>
                    <th>Estado</th>
                    <th>ID Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                function listMedicos($enlace)
                 {
                    $medicossql = "SELECT * FROM medicos";
                    $resultado = $enlace->query($medicossql);
                    return $resultado;
                }
                try {
                    require_once '../conexion/conexionbasededatos.php';
                    $resultado = listMedicos($enlace);
                    if ($resultado->num_rows > 0) {
                        while ($fila_medico = $resultado->fetch_assoc()) {
                        $estado_medico = htmlspecialchars($fila_medico['estado'], ENT_QUOTES, 'UTF-8');
                        echo "<tr>
                        <td>{$fila_medico['idmedico']}</td>
                        <td class='truncate-text' title='" . htmlspecialchars($fila_medico['nombrecompleto']) . "'>" . htmlspecialchars($fila_medico['nombrecompleto']) . "</td>  
                        <td class='truncate-text' title='" . htmlspecialchars($fila_medico['dni']) . "'>" . htmlspecialchars($fila_medico['dni']) . "</td>
                        <td class='truncate-text' title='" . htmlspecialchars($fila_medico['matricula']) . "'>" . htmlspecialchars($fila_medico['matricula']) . "</td>
                        <td>{$fila_medico['consultorio']}</td>
                        <td class='truncate-text' title='" . htmlspecialchars($fila_medico['direcciondomicilio']) . "'>" . htmlspecialchars($fila_medico['direcciondomicilio']) . "</td>
                        <td>{$fila_medico['telefono']}</td>
                        <td class='truncate-text' title='" . htmlspecialchars($fila_medico['correo']) . "'>" . htmlspecialchars($fila_medico['correo']) . "</td>
                        <td class='truncate-text' title='" . htmlspecialchars($fila_medico['especialidad']) . "'>" . htmlspecialchars($fila_medico['especialidad']) . "</td>";
                        $clase_badge_estado = '';
                        if($estado_medico == 'Activo'){
                        $clase_badge_estado = 'text-bg-success';
                        } elseif ($estado_medico == 'Inactivo') {
                        $clase_badge_estado = 'text-bg-danger';
                        } elseif ($estado_medico == 'Licencia') {
                        $clase_badge_estado = 'text-bg-info';
                        }
                        echo   '<td><span class="badge '. $clase_badge_estado . '">' . $estado_medico . '</span>';
                        /*echo    "<td>";<td>{$row['estado']}</td>*/
                        echo "<td>{$fila_medico['idusuario']}</td>
                        <td>
                        <form action='modificar-medico.php' method='post'>
                            <input type='hidden' name='idmedico' value='{$fila_medico['idmedico']}'>
                            <button type='submit' class='boton-modificar'>Modificar</button>
                        </form>"; 
                        if ($estado_medico == 'Activo') {
                        echo "<form action='../acciones/medicos/eliminar_medico.php' method='post' style='display:inline'
                            onsubmit='return confirm(\"¿Estás seguro de que deseas dar de baja a este Medico?\")'>
                            <input type='hidden' name='idmedico' value='{$fila_medico['idmedico']}'>
                            <input type='hidden' name='idusuario' value='{$fila_medico['idusuario']}'>
                            <button type='submit' class='boton-estado-abm'>Dar de Baja</button>
                        </form>";
                        } elseif ($estado_medico== 'Inactivo') {
                        echo "<form action='../acciones/medicos/reactivar_medico.php' method='post' style='display:inline'
                            onsubmit='return confirm(\"¿Estás seguro de que deseas reactivar a este Medico?\")'>
                            <input type='hidden' name='idmedico' value='{$fila_medico['idmedico']}'>
                            <input type='hidden' name='idusuario' value='{$fila_medico['idusuario']}'>
                            <button type='submit' class='boton-estado-reactivar'>Reactivar</button>
                        </form>";
                        }
                    echo    
                        "</td>
                    </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No hay medicos registrados.</td></tr>";
                }
            } catch (Exception $ex){
                echo "<tr><td coldspan='8'>Error: " . $ex->getMessage() . "</td></tr>";   
            }
            ?>      
            </tbody>
            </table>
        </div>
        <!-- 
            <a href="../main/agregar-medico.php" id="agregar">
                <button href="../main/agregar-medico.php" id="agregar" type="submit" class="button">Agregar Médico</button>
            </a>
        -->
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
<div class= footer>
    <h2>Alumno: Tobias Ariel Monzon Proyecto de Centro Medico</h2> 
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>