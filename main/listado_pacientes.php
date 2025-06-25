<?php
session_start();
include('../conexion/conexionbasededatos.php');
// var_dump($_SESSION);
function obtenerListaDePacientes($enlace) {
    $sql = "SELECT * FROM pacientes";
    $resultado = $enlace->query($sql);
    return $resultado;
}
$mensaje_toast = '';
$tipo_toast = '';

if (isset($_GET['success'])) {
    $codigo_efectivo = $_GET['success'];
    switch ($codigo_efectivo) {
        case 'paciente_dado_de_alta':
            $mensaje_toast = '!Paciente dado de alta correctamente!';
            $tipo_toast = 'success';
            break;
        case 'paciente_reactivado':
            $mensaje_toast = '¡Paciente reactivado correctamente!';
            $tipo_toast = 'success';
            break;
        case 'paciente_modificado':
            $mensaje_toast = '¡Paciente modificado correctamente!';
            $tipo_toast = 'success';
            break;
        case 'paciente_registrado':
            $mensaje_toast = '¡Paciente registrado correctamente!';
            $tipo_toast = 'success';
            break;
        default:
            $mensaje_toast = '¡Operacion exitosa!';
            $tipo_toast = 'success';
    }
} elseif (isset($_GET['error'])) {
    $codigo_error = $_GET['error'];
    $detalle_error = isset($_GET['detalle']) ? htmlspecialchars(urldecode($_GET['detalle'])) : '';

    switch ($codigo_error) {
        case 'error_al_dar_alta_paciente':
            $mensaje_toast = 'Error al dar de alta al paciente.';
            $tipo_toast = 'danger';
            break;
        case 'id_paciente_invalido':
            $mensaje_toast = 'ID de paciente no válido.';
            $tipo_toast = 'danger';
            break;
        case 'paciente_en_tratamiento': 
            $mensaje_toast = 'No se puede dar de alta al paciente. Tiene turnos activos o futuros.';
            $tipo_toast = 'warning'; 
            break;
        default:
            $mensaje_toast = 'Ocurrió un error inesperado.';
            $tipo_toast = 'danger';
    }
    if ($detalle_error) {
        $mensaje_toast .= ' Detalle: ' . $detalle_error;
    }
} elseif (isset($_GET['info'])) { 
    $codigo_info = $_GET['info'];
    switch ($codigo_info) {
        case 'paciente_ya_dado_de_alta':
            $mensaje_toast = 'El paciente ya ha sido dado de alta (inactivo para turnos).';
            $tipo_toast = 'info';
            break;
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Pacientes</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="icon" href="../estilos/medicoslista.ico">
</head>
<body>
    <?php include('../funciones/menu_desplegable.php'); ?> <!-- 13/6 Guarde el Menu Desplegable en funciones para que no ocupar menos lineas. -->
    <div class="container">
        <h1 class="paciente">Gestión de Pacientes</h1>
        <div class= "lista-pacientes">
            <h2>Listado de Pacientes</h2>
            <table>
                <thead>
                <tr>
                    <th>ID Paciente</th>
                    <th>Nombre Completo</th>
                    <th>DNI</th>
                    <th>Obra Social</th>
                    <th>Direccion</th>
                    <th>Telefono</th>
                    <th>Correo</th>
                    <th>Notas</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    require_once '../conexion/conexionbasededatos.php';
                    $resultado = obtenerListaDePacientes($enlace);
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                        $idpaciente = htmlspecialchars($row['idpaciente'], ENT_QUOTES, 'UTF-8');
                        $estado_paciente = htmlspecialchars($row['estado'], ENT_QUOTES, 'UTF-8');
                        $clase_fila = '';
                        if ($estado_paciente == 'Activo') {
                            $clase_fila = 'fila-paciente-activo';
                        } elseif ($estado_paciente == 'Inactivo') {
                            $clase_fila = 'fila-paciente-inactivo';
                        }
                        
                        echo '<tr class= "' . $clase_fila . '">';
                        echo "<td>" . $idpaciente . "</td>";
                        echo "<td class='truncate-text' title='" . htmlspecialchars($row['nombrepaciente']) . "'>" . htmlspecialchars($row['nombrepaciente']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['obrasocial'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td class='truncate-text' title='" . htmlspecialchars($row['direccion']) . "'>" . htmlspecialchars($row['direccion']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['telefono'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td class='truncate-text' title='" . htmlspecialchars($row['correoelectronico']) . "'>" . htmlspecialchars($row['correoelectronico']) . "</td>";
                        echo "<td class='truncate-text' title='" . htmlspecialchars($row['notas']) . "'>" . htmlspecialchars($row['notas']) . "</td>";
                        $clase_badge_estado = '';
                        if($estado_paciente == 'En Tratamiento'){
                            $clase_badge_estado = 'text-bg-success';
                        } elseif ($estado_paciente == 'En Espera') {
                            $clase_badge_estado = 'text-bg-danger';
                        } else {
                            $clase_badge_estado = 'text-bg-secondary';
                        }
                        echo '<td><span class="badge '. $clase_badge_estado . '">' . $estado_paciente . '</span></td>';
        
                        echo "<td>";
                        echo "<form action='../main/modificar-paciente.php' method='post' style='display:inline;'>";
                            echo "<input type='hidden' name='idpaciente' value='{$idpaciente}'>";
                            echo "<button type='submit' class='boton-modificar'>Modificar</button>";
                        echo "</form>";
                        if ($estado_paciente == 'En Espera') {
                        echo "<form action='../acciones/pacientes/eliminar_paciente.php' method='post' onsubmit='return confirm(\"¿Estás seguro de que deseas dar de alta a este paciente?\")'>";
                            echo "<input type='hidden' name='idpaciente' value='" . $idpaciente. "'>";
                            echo "<button type='submit' class='boton-estado-abm'>Dar de Alta</button>";
                        echo "</form>";
                        } elseif ($estado_paciente == 'Dado de Alta') {
                        echo "<form action='../acciones/pacientes/reactivar_paciente.php' method='post' onsubmit='return confirm(\"¿Estás seguro de que deseas dar de alta a este paciente?\")'>";
                            echo "<input type='hidden' name='idpaciente' value='" . $idpaciente. "'>";
                            echo "<button type='submit' class='boton-estado-reactivar'>Reactivar</button>";
                        echo "</form>";
                        }
                    echo "</td>";
                echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No hay pacientes registrados.</td></tr>";
                }
            } catch (Exception $ex){
                echo "<tr><td coldspan='8'>Error: " . $ex->getMessage() . "</td></tr>";   
            }
            ?>       
            </tbody>
            </table>
            <a href="pacientes.php">
                <button type="submit" class="boton-registro-datos">Agregar Paciente</button>
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
