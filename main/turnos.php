<?php
session_start();
include('../conexion/conexionbasededatos.php');
if (!isset($_SESSION['nombreusuario'])) {
    header("Location: ../main/inicio-sesion.php"); // Redirige a la página de inicio de sesión si no hay sesión
    exit();
}
$tipo_usuario = $_SESSION['tipousuario'];
include('../funciones/funcionesturnos.php');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Turnos</title>
    <link rel="stylesheet" href="../estilos/estilogestores.css">
</head>
<body>
    <div class="container">
        <h1>Gestion de Turnos</h1>
         <?php if ($tipo_usuario == 'Secretario'): ?>
            <div class="gestion-turnos">
                <h2>Gestion de Turnos</h2>
                <div class="container">    
        <table>
            <thead>
                <tr>
                    <th>ID Turno</th>
                    <th>ID Medico</th>
                    <th>ID Paciente</th>
                    <th>Fecha</th>
                    <th>Lugar</th>
                    <th>Observacion</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $resultadoTurnos = obtenerListaDeTurnos($enlace);
                    if ($resultadoTurnos->num_rows > 0) {
                            while ($filaTurno = $resultadoTurnos->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$filaTurno['idturno']}</td>
                                            <td>{$filaTurno['nombremedico']}</td>
                                            <td>{$filaTurno['nombrepaciente']}</td>
                                            <td>{$filaTurno['fecha']}</td>
                                            <td>{$filaTurno['lugar']}</td>
                                            <td>{$filaTurno['observacion']}</td>
                                            <td>{$filaTurno['estado']}</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No hay turnos registrados.</td></tr>";
                                }
                            } catch (Exception $ex) {
                                echo "<tr><td colspan='7'>Error: " . $ex->getMessage() . "</td></tr>";
                            }
                            ?>
                            <form action= "../main/registro-turnos.php" method="post">
                            <button type="submit">Registrar Nuevo Turno</button>
                            </form>
                        </tbody>
                    </table>
                </div>

            <div>
                <h3>Disponibilidad de Médicos (Agenda)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Médico</th>
                                <th>Fecha</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $resultadoAgenda = obtenerAgendaDeMedicos($enlace);
                                if ($resultadoAgenda->num_rows > 0) {
                                    while ($filaAgenda = $resultadoAgenda->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$filaAgenda['nombrecompleto']}</td>
                                                <td>{$filaAgenda['fechalaboral']}</td>
                                                <td>{$filaAgenda['hora_inicio']}</td>
                                                <td>{$filaAgenda['hora_final']}</td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No hay información de agenda disponible.</td></tr>";
                                }
                            } catch (Exception $ex) {
                                echo "<tr><td colspan='4'>Error: " . $ex->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div>
                    <h3>Notificaciones</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Mensaje</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $resultadoNotificaciones = obtenerNotificaciones($enlace);
                            if ($resultadoNotificaciones->num_rows > 0) {
                                while ($filaNotificacion = $resultadoNotificaciones->fetch_assoc()) {
                                    $estado = $filaNotificacion['visto'] == 0 ? 'No Visto' : 'Visto';
                                    echo "<tr>
                                            <td>{$filaNotificacion['tipo']}</td>
                                            <td>{$filaNotificacion['mensaje']}</td>
                                            <td>{$filaNotificacion['fecha_creacion']}</td>
                                            <td>{$estado}</td>
                                            <td>";
                                    if ($filaNotificacion['visto'] == 0) {
                                        echo "<form method='post' action=''>
                                                <input type='hidden' name='idnotificacion_vista' value='{$filaNotificacion['idnotificacion']}'>
                                                <input type='submit' name='marcar_vista' value='Marcar como Visto'>
                                            </form>";
                                    }
                                    echo "</td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No hay notificaciones.</td></tr>";
                            }

                            if (isset($_POST['marcar_vista'])) {
                                $idNotificacionVista = $_POST['idnotificacion_vista'];
                                marcarNotificacionComoVista($enlace, $idNotificacionVista);
                                header("Location: turnos.php");
                                exit();
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($tipo_usuario == 'Medico'): 
            if (isset($_POST['cancelar_turno'])) {
                $idturno_a_cancelar = $_POST['idturno_cancelar'];
                $idmedico = $_SESSION['idusuario']; 
                if (cancelarTurno($enlace, $idturno_a_cancelar, $idmedico)) {
                    echo "<p style='color:green'>Turno cancelado correctamente. Se ha notificado al secretario.</p>";
                } else {
                    echo "<p style='color:red'>Error al cancelar el turno.</p>";
                }
            }
            if (isset($_POST['modificar_turno'])) {
                $idturno_a_modificar = $_POST['idturno_modificar'];
                $idmedico = $_SESSION['idusuario'];
                $nueva_fecha = $_POST['nueva_fecha'];
                $nueva_hora = $_POST['nueva_hora'];
                if (modificarTurno($enlace, $idturno_a_modificar, $idmedico, $nueva_fecha, $nueva_hora)) {
                    echo "<p style='color:green'>Turno modificado correctamente. Se ha notificado al secretario.</p>";
                } else {
                    echo "<p style='color:red'>Error al modificar el turno.</p>";
                }
            }
            ?>
            <div class="lista-turnos-medico">
                <h2>Lista de Turnos</h2>
                <table>
                <thead>
                    <tr>
                        <th>ID Turno</th>
                        <th>ID Medico</th>
                        <th>ID Paciente</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Observacion</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php
                        try { 
                            $resultadoMedico = obtenerListaDeTurnosIndividual($enlace);
                            if ($resultadoMedico->num_rows > 0) {
                                while ($filaMedico = $resultadoMedico->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$filaMedico['idturno']}</td>
                                            <td>{$filaMedico['nombremedico']}</td>
                                            <td>{$filaMedico['nombrepaciente']}</td>
                                            <td>{$filaMedico['fecha']}</td>
                                            <td>{$filaMedico['lugar']}</td>
                                            <td>{$filaMedico['observacion']}</td>
                                            <td>{$filaMedico['estado']}</td>
                                            <td>
                                                <form method='post' action=''>
                                                    <input type='hidden' name='idturno_cancelar' value='{$filaMedico['idturno']}'>
                                                    <button type='submit' class='boton-modificar' name='cancelar_turno' value='Cancelar' onclick='return confirmarCancelacion({$filaMedico['idturno']})'>
                                                    Cancelar
                                                    </button>
                                                </form>
                                                <form method='post' action=''>
                                                    <input type='hidden' name='idturno_modificar' value='{$filaMedico['idturno']}'>
                                                    Nueva Fecha: <input type='date' name='nueva_fecha'>
                                                    Nueva Hora: <input type='time' name='nueva_hora'>
                                                    <button type='submit' class='boton-modificar' name='modificar_turno' value='Modificar'>
                                                    Modificar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>";    
                                }
                            } else {
                                echo "<tr><td colspan='7'>No tiene turnos asignados.</td></tr>";
                            }
                        } catch (Exception $ex) {
                            echo "<tr><td colspan='7'>Error: " . $ex->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <button onclick="window.location.href='../acciones/generar_pdf_turnos.php'" class="button">Generar PDF de Turnos</button>
                 
                </tr> 
            </div>
        <?php else: ?>
            <p>Acceso no autorizado.</p>
        <?php endif; ?>
        <?php generarBotonRetorno(); //Para el boton de Retorno que aplique a Medicos, Secretarios y Administrador.?> 
    </div>
</body>
</html>
